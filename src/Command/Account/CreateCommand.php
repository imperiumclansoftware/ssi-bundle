<?php

namespace ICS\SsiBundle\Command\Account;

use Doctrine\ORM\EntityManagerInterface;
use ICS\SsiBundle\Entity\Account;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class CreateCommand  extends Command
{

    protected static $defaultName = 'Ssi:User:Create';

    protected $container;

    protected $doctrine;

    protected $encoder;

    private $io;

    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->container = $container;
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $usernameMessage = new TranslatableMessage('User username');
        $userAdminMessage = new TranslatableMessage('User is admin');
        $descriptionMessage = new TranslatableMessage('This command create new user.');

        $this
            ->addArgument('username', InputArgument::OPTIONAL, $usernameMessage)
            ->addOption('admin', 'a', InputOption::VALUE_REQUIRED, $userAdminMessage, null)
            ->setHelp($descriptionMessage);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $admin = $input->getOption('admin');
        $username = $input->getArgument('username');

        $titleMessage = new TranslatableMessage('Create new user');
        $usernameMessage = new TranslatableMessage('Username');
        $passwordMessage = new TranslatableMessage('Password');
        $confirmPasswordMessage = new TranslatableMessage('Confirm poassword');
        $errorPasswordMessage = new TranslatableMessage('Passwords is different !');
        $userAdminMessage = new TranslatableMessage('User is admin');

        $this->io->title($titleMessage);

        while ($username == '') {
            $username = $this->io->ask($usernameMessage);
        }

        $password = '';
        $confirmPassword = '';

        while ($password == '' || $confirmPassword == '' || $password != $confirmPassword) {
            $password = $this->io->askHidden($passwordMessage);
            $confirmPassword = $this->io->askHidden($confirmPasswordMessage);

            if ($password != $confirmPassword) {
                $this->io->error($errorPasswordMessage);
            }
        }

        if ($admin == null) {
            $admin = $this->io->confirm($userAdminMessage);
        }

        $user = new Account();
        $user->setUsername($username);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        if ($admin) {
            $user->AddRole('ROLE_ADMIN');
        }

        $em = $this->doctrine;
        $em->persist($user);
        $em->flush();

        $userMessage = new TranslatableMessage('User');
        $createdMessage = new TranslatableMessage("as created");

        $this->io->success($userMessage . " " . $username . " " . $createdMessage);

        return Command::SUCCESS;
    }
}
