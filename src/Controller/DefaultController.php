<?php

namespace ICS\SsiBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DefaultController extends AbstractController
{
    /**
     * @Route("/login", name="ics_ssi_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger, Request $request): Response
    {
        if ($this->getUser()) {


            return $this->redirectToRoute('homepage');
        }

        // if($this->getParameter('ssi.keyloak.create_unknow_user'))
        // {

        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Ssi/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/login/keycloak", name="ics_ssi_login_keycloak")
     */
    public function loginKeycloak(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('keycloak_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'email',
                'profile',
            ]);
    }

    /**
     * @Route("/logout", name="ics_ssi_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
    * @Route("/keycloak/callback", name="ics_keycloak_check")
    */
    public function callback(ClientRegistry $clientRegistry)
    {
        return $this->render('@Ssi/keycloak.html.twig');
    }
}
