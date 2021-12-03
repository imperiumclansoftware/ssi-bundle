<?php

namespace ICS\SsiBundle\Security;

/**
 * File for login/password authenticator
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use ICS\SsiBundle\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Login/password authenticator class
 *
 * @package SsiBundle\Security
 */
class ADAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    /**
     * Route for login/password form
     */
    public const LOGIN_ROUTE = 'ics_ssi_login';

    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * Url Generator
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * Token manager
     *
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * Password encoder
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * Application container
     *
     * @var ContainerInterface
     */
    private $container;
    /**
     * Log manager
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoginAuthenticator constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
                        EntityManagerInterface $entityManager,
                        ContainerInterface $container,
                        LoggerInterface $logger,
                        UrlGeneratorInterface $urlGenerator,
                        CsrfTokenManagerInterface $csrfTokenManager,
                        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->logger = $logger;
        $this->container = $container;
        $this->config= $container->getParameter('ssi');
    }

    /**
     *  Callback management
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        $baseSupport = self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');

        if($baseSupport && $request->get('domain',false))
        {
            $baseSupport=$baseSupport && $request->get('domain')==$_ENV['ACTIVE_DIRECTORY_DOMAIN'];
        }

        return $baseSupport;
    }
    /**
     * Get credentials for login/password user
     * this method is only called if supports() returns true
     *
     * @param Request $request
     * @return mixed[]
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'domain' => $request->request->get('domain'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    /**
     * Obtain an Account for keycloak user
     *
     * @param mixed[] $credentials
     * @param UserProviderInterface $userProvider
     * @return Account
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $ldap=\ldap_connect("ldap://".$_ENV['ACTIVE_DIRECTORY_HOST']);
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $ldapRdn=$credentials['domain']."\\".$credentials['username'];
        if(@ldap_bind($ldap,$ldapRdn,$credentials['password']))
        {
            $filter="(sAMAccountName=".$credentials['username'].")";
            $result = ldap_search($ldap,$_ENV['ACTIVE_DIRECTORY_BASEDN'],$filter);
            ldap_sort($ldap,$result,"sn");

            foreach(ldap_get_entries($ldap, $result) as $info)
            {
                $aduser=$info;
            }

            $user = $this->entityManager->getRepository(Account::class)->findOneBy(['username' => $credentials['username']]);

            if(!$user && $aduser!= null && $this->config['active_directory']['auto_create_user'])
            {
                $user=new Account();
                $user->setUsername($aduser['samaccountname'][0]);
                $user->setFirstName($aduser['givenname'][0]);
                $user->setLastName($aduser['sn'][0]);
                $user->setAdCreate(true);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->container
                ->get('monolog.logger.db')
                ->info('Create user ' . $user->getUsername().' with active directory authentification.');
            }
            else if(!$user && $aduser==null)
            {
                throw new CustomUserMessageAuthenticationException('Active Directory authentification success but user unknow in local database.');
                return null;
            }

            if($this->config['profileEntity'] != null && $this->config['profileEntity'] != "" && $user->getProfile() == null)
            {
                $user->setProfile(new $this->config['profileEntity']());
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            return $user;
        }
        else
        {
            $this->container->get('monolog.logger.db')->info('Connexion error with username ' . $credentials['username'] . '.');
                // fail authentication with a custom error
                $this->container
                ->get('monolog.logger.db')
                ->warning('Connexion error from ' . $this->container->get('request_stack')->getCurrentRequest()->getClientIp().' with username "'.$credentials['username'].'"');
                throw new CustomUserMessageAuthenticationException('Username could not be found.');
            return null;
        }
    }
    /**
     * Check if login/password has valid
     *
     * @param mixed[] $credentials
     * @param UserInterface $user
     * @return void
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return isset($credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param mixed[] $credentials
     * @return string
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * Execute on authentification success
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param mixed $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $this->container->get('monolog.logger.db')->info('User ' . $token->getUser()->getUsername() . ' connected.');

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    /**
     * Get login/password HMI route
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
