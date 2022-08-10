<?php

namespace App\Controller\Admin;

use App\Entity\CategoryPicture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


class CategoryPictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryPicture::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('designation')->setLabel('nom de l\'album'),
            SlugField::new('slug')->setLabel('Nom url')->setTargetFieldName('designation'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setPageTitle("index", "Mes albums")
        ->setPageTitle('new', 'créer un album')
        ->setPageTitle('edit', 'modifier un album')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
}
