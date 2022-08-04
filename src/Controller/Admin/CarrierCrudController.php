<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CarrierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }

    public function configureCrud ( Crud $crud ) : Crud
    {
        return $crud
            ->setEntityLabelInSingular ('Transporteur')
            ->setEntityLabelInPlural  ('Transporteurs');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name','Nom'),
            IntegerField::new('price','Prix'),
            TextEditorField::new ('description','Description')
        ];
    }

}
