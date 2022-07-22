<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Services\Cart;
use App\Services\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    private $entityManager;
    private $cart;

    public function __construct(EntityManagerInterface $entityManager, Cart $cart)
    {
        $this->entityManager = $entityManager;
        $this->cart = $cart;
    }

    /*
 * ############################
 * LOGIC FOR THE PRODUCTS
 * ###########################
 */


    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/boutique/nos_produits', name: 'app_shop')]
    public function index(Request $request)
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            $products =  $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $products,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/boutique/nos_produits/{slug}', name: 'app_single_product')]
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

    /*
     * ############################
     * LOGIC FOR THE SHOPPING CART SYSTEM
     * ###########################
     */

    // Logic for the cart part //

    #[Route('/boutique/panier', name: 'app_cart')]
    public function myCart(){

        return $this->render('frontoffice/cart.html.twig',[
            'cart'=>$this->cart->getFullCart()
        ]);
    }


    #[Route('/boutique/panier/ajouter/{slug}', name: 'app_add_to_cart')]
    public function addToCart(Request $request,$slug): Response
    {

        $this->cart->add($slug);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/boutique/panier/supprimer_mon_panier', name: 'app_remove_my_cart')]
    public function removeMyCart(){

        $this->cart->clearCart();

        return $this->redirectToRoute('app_shop');
    }

    #[Route('/boutique/panier/supprimer/{slug}', name: 'app_remove_to_cart')]
    public function removeToCart($slug){

        $this->cart->remove($slug);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/boutique/panier/diminuer_quantite/{slug}', name: 'app_decrease_quantity_cart')]
    public function decrease($slug){

        $this->cart->decrease($slug);
        return $this->redirectToRoute("app_cart");
    }

    /*
 * ############################
 * LOGIC FOR THE SHOPPING CART SYSTEM
 * ###########################
 */

    #[Route('/boutique/commande', name: 'app_checkout')]
    public function checkout(){

        if(!$this->getUser()->getAddressLivraisons()->getValues()){
            return $this->redirectToRoute('app_account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user'=>$this->getUser()
        ]);
        return $this->render('frontoffice/checkout.html.twig',[
            'form'=>$form->createView()
        ]);
    }



}
