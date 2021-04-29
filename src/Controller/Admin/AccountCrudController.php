<?php

namespace ICS\SsiBundle\Controller\Admin;

/**
 * File for Easyadmin Account management
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use ICS\SsiBundle\Entity\Account;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Easyadmin Account management
 *
 * @package SsiBundle\Controller\Admin
 */
class AccountCrudController extends AbstractCrudController
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
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFields(string $pageName): iterable
    {
        $hierarchy = $this->container->get('parameter_bag')->get('security.role_hierarchy.roles');

        $roles = $this->getRoles($hierarchy);

        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('username')
                ->setLabel('Username')
                ->setHelp('Username was unique in database.'),
            EmailField::new('email')
                ->setLabel('E-Mail')
                ->setHelp('E-Mail was unique in database.'),
            BooleanField::new('keycloakCreate')
                ->hideOnDetail()
                ->hideOnForm(),
            TextField::new('firstname'),
            TextField::new('lastname'),
            ChoiceField::new('roles')
                ->setChoices($roles)
                ->allowMultipleChoices()
                ->setLabel('Roles')
                ->setHelp('Define user roles for website activity.'),
            TextField::new('password')
                ->hideOnIndex()
                ->setFormType(PasswordType::class)
                ->setLabel('Password')
                ->setHelp('Password complexity is necessary dor security.'),


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureFilters(Filters $filters): Filters
    {
        $hierarchy = $this->container->get('parameter_bag')->get('security.role_hierarchy.roles');

        $roles = $this->getRoles($hierarchy);

        return $filters
            ->add('username')
            ->add(
                ChoiceFilter::new('roles')
                    ->setChoices($roles)
            );
    }

    /**
     * Method for compute account roles
     *
     * @param array $roles
     * @return array
     */
    public function getRoles($roles)
    {
        $result = [];
        foreach ($roles as $key => $role) {
            if (\is_array($role)) {
                $result = array_merge($result, $this->getRoles($role));
                $result[$key] = $key;
            } else {
                $result[$role] = $role;
            }
        }
        ksort($result);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function persistEntity(EntityManagerInterface $doctrine, $user): void
    {
        if($user->getPassword()!=null)
        {
            $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
        }
        parent::persistEntity($doctrine, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateEntity(EntityManagerInterface $doctrine, $user): void
    {
        if($user->getPassword()!=null)
        {
            $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
        }
        parent::updateEntity($doctrine, $user);
    }
}
