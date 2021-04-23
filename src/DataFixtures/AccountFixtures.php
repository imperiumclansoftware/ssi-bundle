<?php

namespace ICS\SsiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ICS\SsiBundle\Entity\Account;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new Account();
        $admin->setUsername('admin')
                ->setEmail('administrateur@example.com')
                ->setFirstName("Administrateur")
                ->setPassword($this->encoder->encodePassword($admin,'adminPassword'))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $manager->flush();

        for($i=1;$i<=10;$i++)
        {
            $user=new Account();
            $user->setUsername('user'.$i)
                    ->setEmail('user'.$i.'@example.com')
                    ->setFirstName("Utilisateur")
                    ->setLastName('Numero '.$i)
                    ->setPassword($this->encoder->encodePassword($user,'userPassword'))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}