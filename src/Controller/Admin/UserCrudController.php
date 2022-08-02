<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstname')->setLabel('Nom'),
            TextField::new('lastname')->setLabel('Prenom'),
            EmailField::new('email'),
            ArrayField::new('roles'),
            // ChoiceField::new('roles')->setChoices([
            //     'User' => 'ROLE_USER',
            //     'Employes' => 'ROLE_ADMIN',
            //     'Administrateur' => 'ROLE_SUPER_ADMIN'
            // ])->autocomplete()
            // ->hideOnIndex(),
            BooleanField::new('rgpd'),
            BooleanField::new('isVirified')->setLabel('compte active'),
        ];
    }
    

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }
    
}
