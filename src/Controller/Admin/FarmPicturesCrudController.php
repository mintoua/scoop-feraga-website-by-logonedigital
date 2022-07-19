<?php

namespace App\Controller\Admin;

use App\Entity\FarmPictures;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FarmPicturesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FarmPictures::class;
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
