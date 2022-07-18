<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('product_name'),
            MoneyField::new('product_price')->setCurrency('XAF'),
            IntegerField::new('product_quantity'),
            AssociationField::new('category'),
            ImageField::new('product_image')->setUploadDir('\public\uploads\images')->setFormTypeOptions(['mapped'=>false, 'required'=>false]),
            TextEditorField::new('product_description'),

        ];
    }

}
