<?php

namespace App\Controller\Admin;

use App\Entity\FarmPictures;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class FarmPicturesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FarmPictures::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('designation'),
            SlugField::new('slug')->setTargetFieldName('designation'),
            AssociationField::new('categoryPicture'),
            ImageField::new('link')->setBasePath(' uploads/')
                ->setUploadDir('public/uploads/FarmImages')
                ->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            TextEditorField::new('description'),
        ];
    }

   
    
}
