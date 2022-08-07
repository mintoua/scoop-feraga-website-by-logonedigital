<?php

namespace App\Controller\Admin;

use App\Entity\Posts;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title')->hideWhenUpdating()->hideWhenCreating(),
            ImageField::new('post_image')->setBasePath('uploads\images')->setUploadDir('public\uploads\images'),
            TextareaField::new('description')->setFormType(CKEditorType::class)->hideOnIndex(),
            AssociationField::new('postCategory'),
            DateTimeField::new('createdAt')->hideOnForm(),
            TextField::new('description')->stripTags()->setLabel('Contenu'),
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title'])
            ->setAutofocusSearch();
    }

}
