<?php

namespace ICS\SsiBundle\Controller\Admin;

/**
 * File for Easyadmin Account management
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use ICS\SsiBundle\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Doctrine\ORM\EntityManagerInterface;

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

        $result =  [
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
            BooleanField::new('AdCreate')
                ->hideOnDetail()
                ->hideOnForm(),
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
            AssociationField::new('profile')
                ->hideOnIndex()

        ];

        //TODO : Add Profile administration
        //$result = array_merge($result,)
    
        return $result;
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
            )
            ->add(BooleanFilter::new('keycloakCreate'))
            ->add(BooleanFilter::new('AdCreate'))
        ;
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
            $encodedPassword = $this->encoder->hashPassword($user, $user->getPassword());
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
            $encodedPassword = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
        }
        parent::updateEntity($doctrine, $user);
    }
}
