<?php

namespace App\Controller\Admin;

use App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\Cache\CacheInterface;
use function Clue\StreamFilter\fun;

class ProductCategoryCrudController extends AbstractCrudController
{
    private $cache ;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public static function getEntityFqcn(): string
    {
        return ProductCategory::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX,Action::EDIT, function(Action $action){
                $this->cache->delete('categories_all');
                return $action;
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function(Action $action){
                $this->cache->delete('categories_all');
                return $action;
            })
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
        ];
    }

}
