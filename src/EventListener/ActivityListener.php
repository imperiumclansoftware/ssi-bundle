<?php

namespace ICS\SsiBundle\EventListener;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ICS\SsiBundle\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

class ActivityListener implements EventSubscriberInterface
{
	protected $tokenStorage;
    protected $doctrine;

    public function __construct(TokenStorageInterface  $tokenStorage, EntityManagerInterface $doctrine)
    {
        $this->tokenStorage = $tokenStorage;
        $this->doctrine = $doctrine;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::FINISH_REQUEST => [
                ['onKernelFinishRequest', -10]
            ]
        ];
    }

    /**
    * Update the user "lastActivity" on each request
    * @param ResponseEvent $event
    */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
		$user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof Account) {
            $user->setLastActivity(new \DateTime());
            $this->doctrine->persist($user);
            $this->doctrine->flush();
		}
    }
}