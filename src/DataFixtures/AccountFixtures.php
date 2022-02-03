<?php

namespace ICS\SsiBundle\DataFixtures;

/**
 * File for Account fixtures
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ICS\SsiBundle\Entity\Account;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class for Account fixtures
 *
 * @package SsiBundle\DataFixtures
 */
class AccountFixtures extends Fixture
{
    /**
     * Encoder for Account password
     *
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    /**
     * Class constructor
     *
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $admin = new Account();
        $admin->setUsername('admin')
                ->setEmail('administrateur@example.com')
                ->setPassword($this->encoder->hashPassword($admin,'adminPassword'))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $manager->flush();

        for($i=1;$i<=10;$i++)
        {
            $user=new Account();
            $user->setUsername('user'.$i)
                    ->setEmail('user'.$i.'@example.com')
                    ->setPassword($this->encoder->hashPassword($user,'userPassword'))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}