<?php

namespace App\Controller\Admin;

use App\Entity\Posts;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class PostsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Posts::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title'])
            ->setAutofocusSearch()
            ->setPageTitle('index', 'gérer vos articles')
            ->setPageTitle('new', 'ajouter un article')
            ->setPageTitle('edit', 'modifier un article')
            ->setPageTitle('detail', "détail d'un article")
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add ('postCategory')
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            TextField::new('title')->setLabel('titre article'),
            SlugField::new('slug')->setTargetFieldName('title')->hideOnForm(),
            ImageField::new('post_image')
                ->setLabel('image')
                ->setBasePath('uploads/images')
                ->setUploadDir('public/uploads/images')
                ->setUploadedFileNamePattern('[randomhash].[extension]')->setRequired(false),
            AssociationField::new('postCategory')->setLabel('thématique'),
            DateTimeField::new('createdAt')->hideOnForm(),
            TextareaField::new('description')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex()
                ->renderAsHtml(),
        ];
    }


    // public function configureFilters(Filters $filters): Filters
    // {
    //     return $filters
    //         ->add(DateTimeFilter::new('createdAt'))
    //         ->add(DateTimeFilter::new('updatedAt'))
    //         ->add(BooleanFilter::new('isVirified'))
    //         ->add(BooleanFilter::new('blocked'))
    //     ;
    // }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
        ;
    }

}
