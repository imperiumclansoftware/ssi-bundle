<?php

namespace ICS\SsiBundle\Util;

use Doctrine\ORM\EntityManagerInterface;
use ICS\SsiBundle\Entity\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MonologDBHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected $token;

    /**
     * MonologDBHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token = null)
    {
        parent::__construct();
        $this->em = $em;
        $this->token = $token;
    }

    /**
     * Called when writing to our database
     * @param array $record
     */
    protected function write(array $record): void
    {
        $user = null;
        if ($this->token != null && $this->token->getToken() != null) {
            $user = $this->token->getToken()->getUser();
        }

        $logEntry = new Log();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setContext($record['context']);

        if ($user != null) {
            $logEntry->setUsername($user->getUsername());
        }

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
