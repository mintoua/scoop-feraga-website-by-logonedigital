<?php

namespace App\Controller\Admin;

use App\Entity\CategoryPicture;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryPictureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryPicture::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
