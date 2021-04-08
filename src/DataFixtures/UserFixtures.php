<?php

namespace ICS\SsiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use ICS\SsiBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setUsername('admin')
                ->setPassword($this->encoder->encodePassword($admin,'adminPassword'))
                ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $manager->flush();

        for($i=1;$i<=10;$i++)
        {
            $user=new User();
            $user->setUsername('user'.$i)
                    ->setPassword($this->encoder->encodePassword($user,'userPassword'))
                    ->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}