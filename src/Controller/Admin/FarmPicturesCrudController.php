<?php

namespace App\Controller\Admin;

use App\Entity\FarmPictures;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
            TextField::new('designation')->setLabel('nom photo'),
            SlugField::new('slug')->setTargetFieldName('designation'),
            AssociationField::new('categoryPicture')->setLabel("album"),
            ImageField::new('link')->setBasePath('uploads/FarmImages')
                ->setUploadDir('public/uploads/FarmImages')->setLabel('images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            TextareaField::new('description')
                ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex()
                ->renderAsHtml()
                ,
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setPageTitle('index', 'gérer vos albums')
        ->setPageTitle('new', "Ajouter une photo")
        ->setPageTitle('edit', "modifier une photo");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }



   
    
}
