<?php

namespace ICS\SsiBundle\Command\Account;

/**
 * File for change password command
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Change password command class
 *
 * @package SsiBundle\Command\Account
 */
class ChangePasswordCommand  extends Command
{
    /**
     * Command default name
     *
     * @var string
     */
    protected static $defaultName = 'Ssi:User:ChangePassword';
    /**
     * Command application container
     *
     * @var ContainerInterface
     */
    protected $container;
    /**
     * Entity manager for data access
     *
     * @var EntityManagerInterface
     */
    protected $doctrine;

    /**
     * Class for console HMI
     *
     * @var SymfonyStyle
     */
    private $io;

    /**
     * Class constructor
     *
     * @param ContainerInterface $container
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine)
    {
        parent::__construct();

        $this->container = $container;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     *
     * @todo Review change password Arguments
     */
    protected function configure()
    {
        // TODO : Review change password Arguments
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Youtube Url')
            ->setHelp('This command download all publications of an Instagram account from official Instagram website');
    }

    /**
     * {@inheritdoc}
     *
     * @todo Write change password execution
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Change user password.');

        // TODO : Write change password execution

        return Command::SUCCESS;
    }
}
