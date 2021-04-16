<?php

namespace ICS\SsiBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use ICS\SsiBundle\Entity\Account;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountCrudController extends AbstractCrudController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getEntityFqcn(): string
    {
        return Account::class;
    }


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

    public function persistEntity(EntityManagerInterface $doctrine, $user): void
    {
        $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        parent::persistEntity($doctrine, $user);
    }

    public function updateEntity(EntityManagerInterface $doctrine, $user): void
    {
        $encodedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);

        parent::updateEntity($doctrine, $user);
    }
}
