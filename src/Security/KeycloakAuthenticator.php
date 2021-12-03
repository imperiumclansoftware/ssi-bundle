<?php

namespace ICS\SsiBundle\Security;

/**
 * File for keycloak authenticator
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\EntityManagerInterface;
use ICS\SsiBundle\Entity\Account;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient;
use Stevenmaguire\OAuth2\Client\Provider\KeycloakResourceOwner;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Keycloak Authenticator
 *
 * @package SsiBundle\Security
 */
class KeycloakAuthenticator extends SocialAuthenticator
{
    /**
     * Keycloak user registry
     *
     * @var ClientRegistry
     */
    private $clientRegistry;
    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * Route manager
     *
     * @var RouterInterface
     */
    private $router;
    /**
     * Application container
     *
     * @var ContainerInterface
     */
    private $config;

    /**
     * KeycloakAuthenticator constructor
     *
     * @param ClientRegistry $clientRegistry
     * @param EntityManagerInterface $em
     * @param RouterInterface $router
     * @param ContainerInterface $container
     */
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, ContainerInterface $container)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
	    $this->router = $router;
        $this->config= $container->getParameter('ssi');
    }

    /**
     * Callback management
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'ics_keycloak_check';
    }

    /**
     * Get credentials for keycloak user
     * this method is only called if supports() returns true
     *
     * @param Request $request
     * @return League\OAuth2\Client\Token\AccessToken|void
     */
    public function getCredentials(Request $request)
    {


        return $this->fetchAccessToken($this->getKeycloakClient());
    }

    /**
     * Obtain an Account for keycloak user
     *
     * @param League\OAuth2\Client\Token\AccessToken $credentials
     * @param UserProviderInterface $userProvider
     * @return Account
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /* @var KeycloakResourceOwner $keycloakUser */
        $keycloakUser = $this->getKeycloakClient()
            ->fetchUserFromToken($credentials);

        $email = $keycloakUser->getEmail();

        // Test if user exist
        $user = $this->em->getRepository(Account::class)
            ->findOneBy(['email' => $email]);
        if($user==null && $this->config['keycloak']['auto_create_user'])
        {
            // Else create one
            $user=new Account();
            $user->setEmail($keycloakUser->getEmail());
            $user->setKeycloakCreate(true);

        }

        if($user!=null)
        {
            // Update user Infos for keyloack
            $tmpUser=$keycloakUser->toArray();
            $user->setUsername($tmpUser['preferred_username']);
            $user->setFirstname($tmpUser['given_name']);
            $user->setLastname($tmpUser['family_name']);
            $this->em->persist($user);
            $this->em->flush();
        }
        
        if($this->config['profileEntity'] != null && $this->config['profileEntity'] != "" && $user->getProfile() == null)
        {
            $user->setProfile(new $this->config['profileEntity']());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    /**
     * Obtain keycloak client
     *
     * @return KeycloakClient
     */
    private function getKeycloakClient()
    {
        return $this->clientRegistry
            ->getClient('keycloak_main');
	}

    /**
     * Execute on oAuth success
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param mixed $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate('homepage');

        return new RedirectResponse($targetUrl);
    }

    /**
     * Execute on oAuth Failure
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse(
            $this->router->generate('ics_ssi_login'), // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     *
     * @param Request $request
     * @param AuthenticationException $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('ics_ssi_login_keycloak'), // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}