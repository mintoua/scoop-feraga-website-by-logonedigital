<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarrierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add (Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ;
    }
    public function configureCrud ( Crud $crud ) : Crud
    {
        return $crud
            ->setPageTitle('index', 'Gérer Les Transporteurs')
            ->setPageTitle('edit', "Modifier les informations du transporter")
            ->setPageTitle ('detail','Détails')
            ->setEntityLabelInSingular ('Transporteur')
            ->setEntityLabelInPlural  ('Transporteurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name','Nom'),
            IntegerField::new('price','Prix(en FCFA)'),
            TextareaField::new ('description','Description')
        ];
    }

}
