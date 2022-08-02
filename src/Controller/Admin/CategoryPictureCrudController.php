<?php

namespace App\Controller\Admin;

use App\Entity\CategoryPicture;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;


class CategoryPictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryPicture::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('designation')->setLabel('désignation'),
            SlugField::new('slug')->setLabel('Nom url')->setTargetFieldName('designation'),
        ];
    }
    
}
