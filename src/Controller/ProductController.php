<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/nos_produits', name: 'app_shop')]
    public function index(): Response
    {
        $products =  $this->entityManager->getRepository(Product::class)->findAll();


        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/nos_produits/{slug}', name: 'app_single_product')]
    public function singleProduct($slug)
    {
        $product =  $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product){
            return $this->redirectToRoute('app_shop');
        }
        return $this->render('frontoffice/single_product.html.twig', [
            'product' => $product,
        ]);
    }

}
