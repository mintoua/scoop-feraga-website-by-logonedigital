<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\Comments;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Services\BoutiqueService;
use App\Services\Cart;

use App\Entity\Product;
use App\Entity\ProductCategory;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * ####### HERE YOU WILL FIND ############
 * ####### ALL THE LOGIC RELATED TO THE SHOP PART  ####
 * ####### ie Products, Orders etc. #######
 *
 */

class BoutiqueController extends AbstractController
{
    private $entityManager;
    private $cart;
    private $cache;
    private $flashy;
    private $BoutiqueService;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cache, Cart $cart, FlashyNotifier $flashy, BoutiqueService $service)
    {
        $this->entityManager = $entityManager;
        $this->cart = $cart;
        $this->cache= $cache;
        $this->flashy = $flashy;
        $this->BoutiqueService = $service;
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
        $categories = $this->cache->get('product_categories_list', function (ItemInterface $item){
            $item->expiresAfter(3600);
            return $this->entityManager->getRepository(ProductCategory::class)->findAll();
        });
        $products =  $this->cache->get('product_list', function (ItemInterface $item){
            $item->expiresAfter(3600);
            return $this->entityManager->getRepository(Product::class)->findAll();
        });

        $filters = $request->get("categories");
        $limit = 1;

        if($request->get('ajax')){
            if($filters != null){
                $products = $this->entityManager->getRepository(Product::class)->productsFiltered($filters);

                return new JsonResponse([
                    'content'=> $this->renderView('frontoffice/product_list.html.twig', [
                        'products' => $this->BoutiqueService->toPaginate($products,$request,$limit)
                    ])
                ]);
            }
            else{
                return new JsonResponse([
                    'content'=> $this->renderView('frontoffice/product_list.html.twig', [
                        'products' => $this->BoutiqueService->toPaginate($products,$request,$limit)
                    ])
                ]);
            }
        }
        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $this->BoutiqueService->toPaginate($products,$request,$limit),
            'categories'=>$categories,
        ]);
    }


    #[Route('/boutique/nos_produits/{slug}', name: 'app_single_product')]
    public function singleProduct(Request $request, $slug)
    {
        $product =  $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        $comments = $this->entityManager->getRepository(Comments::class)->findComments($product);

        $data = $request->request->all();
        $token = $request->request->get('token');

        if(!$product){
            return $this->redirectToRoute('app_shop');
        }
        if($this->isCsrfTokenValid('add-comment', $token) && $data){
                $this->BoutiqueService->persistComment($data["message"],$data["rating"],$this->getUser(),$product);
                $comments = $this->entityManager->getRepository(Comments::class)->findComments($product);
                return $this->render('frontoffice/comments_list.html.twig', [
                    'product' => $product,
                    'comments'=> $comments
                ]);
        }


        return $this->render('frontoffice/single_product.html.twig', [
            'product' => $product,
            'comments' => $comments
        ]);
    }

    #[Route('/boutique/produits/', name: 'app_shop_search')]
    public function searchedProduct(Request $request){

        $products = $this->entityManager->getRepository(Product::class)->productSearch($request->get('searchValue'));

        return $this->render('frontoffice/product_list.html.twig',[
            'products'=>$products
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
        $this->flashy->success('Ajouter au panier avec succes','');
        return $this->redirectToRoute('app_shop');
    }

    #[Route('/boutique/panier/supprimer_mon_panier', name: 'app_remove_my_cart')]
    public function removeMyCart(){

        $this->cart->clearCart();

        return $this->redirectToRoute('app_shop');
    }

    #[Route('/boutique/panier/supprimer/{slug}', name: 'app_remove_to_cart')]
    public function removeToCart($slug){

        $this->cart->remove($slug);
        if(count($this->cart->getFullCart()) > 0){
            return $this->redirectToRoute('app_cart');
        }
        return $this->redirectToRoute('app_shop');
    }

    #[Route('/boutique/panier/diminuer_quantite/{slug}', name: 'app_decrease_quantity_cart')]
    public function decrease($slug){

        $this->cart->decrease($slug);

        return $this->redirectToRoute("app_cart");
    }

    #[Route('/boutique/panier/augmenter_quantite/{slug}', name: 'app_encrease_quantity_cart')]
    public function encrease(Request $request,$slug): Response
    {

        $this->cart->add($slug);

        return $this->redirectToRoute('app_cart');
    }


    /*
 * ############################
 * LOGIC FOR THE SHOPPING CART SYSTEM
 * ###########################
 */

    #[Route('/boutique/commande', name: 'app_checkout')]
    public function checkout(Request $request){

        if(!$this->getUser()->getAddressLivraisons()->getValues()){
            return $this->redirectToRoute('app_account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user'=>$this->getUser()
        ]);

        return $this->render('frontoffice/checkout.html.twig',[
            'form'=>$form->createView(),
            'cart'=>$this->cart->getFullCart(),
            'total'=>$this->cart->getTotal()
        ]);
    }

    #[Route('/boutique/commande/ajouter', name: 'app_add_order', methods: 'POST')]
    public function addOrder(Request $request){

        $form = $this->createForm(OrderType::class, null, [
            'user'=>$this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->get('submit')->isClicked()) {

            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getfirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();

            if($delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();

            //add order
            $order = new Order();
            $order->setReference($date->format('dmY').'-'.uniqid());
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0); // state = 0 non validé encore


            $this->entityManager->persist($order);

            //add orderDetails
            foreach ($this->cart->getFullCart() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getProductName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getProductPrice());
                $orderDetails->setTotal($product['product']->getProductPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

       //     $this->entityManager->flush();

            return $this->render('frontoffice/final_checkout.html.twig',[
                'cart'=>$this->cart->getFullCart(),
                'total'=>$this->cart->getTotal(),
                'carrier'=>$carriers,
                'delivery'=>$delivery_content
            ]);
        }


        return $this->redirectToRoute('app_cart');

    }


    #[Route('/boutique/commande/valider', name: 'app_order_saved')]
    public function savedOrder()
    {
        // ICI NORMALEMENT ON EFFECTUE LE PAIEMENT PUIS ENVOI UN MAIL
     /*   $mail = new Mail();
        $content = "Bonjour".$this->getUser()->getFirstname()."<br/>Merci pour votre commande";
        $mail->send($this->getUser()->getUsername(),$this->getUser()->getFirstname(),'Votre commande SCOOPS FERAGA est bien validée.', $content);*/

        $this->cart->clearCart();
        return $this->redirectToRoute('app_shop');
    }

}
