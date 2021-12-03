<?php

namespace ICS\SsiBundle\DataFixtures;

/**
 * File for Account fixtures
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ICS\SsiBundle\Entity\Account;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * Class constructor
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
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
                ->setPassword($this->encoder->encodePassword($admin,'adminPassword'))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $manager->flush();

        for($i=1;$i<=10;$i++)
        {
            $user=new Account();
            $user->setUsername('user'.$i)
                    ->setEmail('user'.$i.'@example.com')
                    ->setPassword($this->encoder->encodePassword($user,'userPassword'))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}