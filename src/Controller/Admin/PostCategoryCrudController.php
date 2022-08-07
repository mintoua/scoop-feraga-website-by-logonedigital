<?php

namespace App\Controller\Admin;

use App\Entity\PostCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

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
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name')->hideWhenUpdating()->hideWhenCreating(),
            TextareaField::new('category_description')
                ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
            // ImageField::new('post_category_image')->setBasePath('uploads\images')->setUploadDir('public\uploads\images'),
        ];
    }

}
