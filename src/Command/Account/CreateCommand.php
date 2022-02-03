<?php

namespace ICS\SsiBundle\Command\Account;

/**
 * File for create account command
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use ICS\SsiBundle\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Create account command class
 *
 * @package SsiBundle\Command\Account
 */
class CreateCommand  extends Command
{
    /**
     * Command default name
     *
     * @var string
     */
    protected static $defaultName = 'Ssi:User:Create';
    /**
     * Entity manager for data access
     *
     * @var EntityManagerInterface
     */
    protected $doctrine;
    /**
     * Class for account password encodage
     *
     * @var UserPasswordHasherInterface
     */
    protected $encoder;
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
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(EntityManagerInterface $doctrine, UserPasswordHasherInterface $encoder)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
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
        $user->setPassword($this->encoder->hashPassword($user, $password));
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
