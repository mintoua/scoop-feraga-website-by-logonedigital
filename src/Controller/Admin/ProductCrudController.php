<?php

namespace App\Controller\Admin;

use App\Entity\Product;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add (Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('category')
            ->add ('product_quantity')
            ->add('product_price')
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular ('Produit')
            ->setEntityLabelInPlural ('Produits');
    }

    public function configureFields(string $pageName): iterable
    {
       return [
            TextField::new('product_name','Nom du Produit'),
            SlugField::new('slug')->setTargetFieldName('product_name')->hideOnIndex ()->hideWhenUpdating (),
            IntegerField::new('product_price','Prix(en FCFA)'),
            IntegerField::new('product_quantity','Quantité'),
           BooleanField::new ('isBest','Mettre à la une'),
            AssociationField::new('category')->setLabel ("Catégorie"),
            ImageField::new('product_image','Image')->setBasePath('uploads\images')
                ->setUploadDir('public\uploads\images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            TextareaField::new('product_description','Description')
                ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                ->setFormType (CKEditorType::class)
                ->hideOnIndex ()
                ->renderAsHtml (),

        ];
    }

}
