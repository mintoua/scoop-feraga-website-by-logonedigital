<?php

namespace App\Controller\Admin;

use App\Entity\PostCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostCategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostCategory::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

           // IdField::new('id'),
            TextField::new('name')->setLabel("thématique"),
            SlugField::new('slug')->setTargetFieldName('name')->hideWhenUpdating()->hideWhenCreating(),
            TextareaField::new('category_description')
                ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex()
                ->renderAsHtml()
                ->setLabel('description thématique')
                ,
            // ImageField::new('post_category_image')->setBasePath('uploads\images')->setUploadDir('public\uploads\images'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setPageTitle("index", "gérer vos thématiques")
        ->setPageTitle("edit", "modifier une thématique")
        ->setPageTitle("detail", "détail")
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add('index', Action::DETAIL);
    }

}
