<?php

namespace ICS\SsiBundle\Controller;

/**
 * File of controllers for authentifications
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controllers for authentifications
 *
 * @package SsiBundle\Controller
 */
class AuthentificationController extends AbstractController
{
    /**
     * Controller for Login/Password authentification
     *
     * @return Response
     * @Route("/login", name="ics_ssi_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Ssi/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'keycloak' => isset($_ENV['KEYCLOAK_URL']),
        ]);
    }

    /**
     * Controller for keycloak authentification
     *
     * @return Response
     * @Route("/login/keycloak", name="ics_ssi_login_keycloak")
     */
    public function loginKeycloak(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('keycloak_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(['email','profile'],[]);
    }

    /**
     * Controller for logout
     *
     * @return Response
     * @Route("/logout", name="ics_ssi_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Controller for keycloak authentification callback
     *
     * @return Response
     * @Route("/keycloak/callback", name="ics_keycloak_check")
     */
    public function callback(ClientRegistry $clientRegistry)
    {
        return $this->render('@Ssi/keycloak.html.twig');
    }
}
