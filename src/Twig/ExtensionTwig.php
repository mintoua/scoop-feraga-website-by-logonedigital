<?php

namespace App\Twig;

use App\Entity\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExtensionTwig extends AbstractExtension
{

    private  $entityManager;

    public function __construct (EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions ()
    {
        return [
            new TwigFunction('ProductCategories',[$this, 'getProductCategories'])
        ];
    }

    public function getProductCategories(){

        return $this->entityManager->getRepository (ProductCategory::class)->findAll ();
    }
}