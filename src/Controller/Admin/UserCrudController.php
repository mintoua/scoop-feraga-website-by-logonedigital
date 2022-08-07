<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname')->setLabel('NOM'),
            TextField::new('lastname')->setLabel('PRENOM'),
            EmailField::new('email')->setLabel('EMAIL'),
            ChoiceField::new('roles')->setChoices([
                'User' => 'ROLE_USER',
                'Employes' => 'ROLE_ADMIN',
                'Administrateur' => 'ROLE_SUPER_ADMIN'
            ])->allowMultipleChoices()->setLabel('ROLE(S)'),
            BooleanField::new('rgpd')->setLabel('RGPD')->hideOnIndex(),
            BooleanField::new('isVirified')->setLabel('COMPTE ACTIVE')->hideOnIndex(),
            BooleanField::new('blocked')->setLabel('BLOQUE')->hideOnIndex(),
            DateTimeField::new('createdAt')->setLabel('DATE DE CREATION')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('updatedAt')->setLabel('DERNIERE MISE A JOUR')->hideOnForm()->hideOnIndex()
        ];
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ->add(BooleanFilter::new('isVirified'))
            ->add(BooleanFilter::new('blocked'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['lastname', 'firstname', 'email'])
            ->setAutofocusSearch();
    }
    
    
}
