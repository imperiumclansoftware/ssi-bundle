<?php

namespace ICS\SsiBundle\Command\User;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChangePasswordCommand  extends Command
{

    protected static $defaultName = 'Ssi:User:ChangePassword';

    protected $container;

    protected $doctrine;

    private $io;

    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine)
    {
        parent::__construct();

        $this->container = $container;
        $this->doctrine = $doctrine;
    }

    protected function configure()
    {
        $this
            ->addArgument('url', InputArgument::REQUIRED, 'Youtube Url')
            ->setHelp('This command download all publications of an Instagram account from official Instagram website');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);


        return Command::SUCCESS;
    }
}
