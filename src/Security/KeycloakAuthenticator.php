<?php

namespace ICS\SsiBundle\Security;


use Doctrine\ORM\EntityManagerInterface;
use ICS\SsiBundle\Entity\Account;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient;
use Stevenmaguire\OAuth2\Client\Provider\KeycloakResourceOwner;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class KeycloakAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    private $config;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
	    $this->router = $router;
        //$this->config= $parameters->get('framework.ssi');
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'ics_keycloak_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true

        return $this->fetchAccessToken($this->getKeycloakClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /* @var KeycloakResourceOwner $keycloakUser */
        $keycloakUser = $this->getKeycloakClient()
            ->fetchUserFromToken($credentials);

        $email = $keycloakUser->getEmail();

        // Test if user exist
        $user = $this->em->getRepository(Account::class)
            ->findOneBy(['email' => $email]);
        if($user==null && $this->config['keycloak_enabled'])
        {
            // Else create one
            $user=new Account();
            $user->setEmail($keycloakUser->getEmail());

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
        return $user;
    }

    /**
     * @return KeycloakClient
     */
    private function getKeycloakClient()
    {
        return $this->clientRegistry
            ->getClient('keycloak_main');
	}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('homepage');

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            $this->router->generate('ics_ssi_login_keycloak'), // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    // ...
}