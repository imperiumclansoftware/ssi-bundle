<?php

namespace ICS\SsiBundle\Controller\Admin;

/**
 * File for Easyadmin Log management
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use ICS\SsiBundle\Entity\Log;

/**
 * Easyadmin Log management
 *
 * @package SsiBundle\Controller\Admin
 */
class LogCrudController extends AbstractCrudController
{
     /**
     * @inheritDoc
     */
    public static function getEntityFqcn(): string
    {
        return Log::class;
    }

    /**
     * @inheritDoc
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id'),
            DateTimeField::new('createdAt'),
            TextField::new('message'),
            TextField::new('levelName'),
            TextField::new('username'),
        ];
    }

     /**
     * @inheritDoc
     */
    public function configureFilters(Filters $filters): Filters
    {
        $levels = ['INFO' => 'INFO', 'WARNING' => 'WARNING', 'ERROR' => 'ERROR', 'CRITICAL' => 'CRITICAL'];

        return $filters
            ->add(DateTimeFilter::new('createdAt'))
            ->add('message')
            ->add('username')
            ->add(
                ChoiceFilter::new('levelName')
                    ->setChoices($levels)
            );
    }

     /**
     * @inheritDoc
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }
}
