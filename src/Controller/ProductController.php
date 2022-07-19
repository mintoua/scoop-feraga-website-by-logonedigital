<?php

namespace App\Controller;

use App\Classes\Search;
use App\Entity\Product;
use App\Form\SearchType;
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

        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);
        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $products,
            'form'=>$form->createView()
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
