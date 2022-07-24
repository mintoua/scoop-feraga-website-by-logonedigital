<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Services\Cart;
use App\Services\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class,$search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            $products =  $this->entityManager->getRepository(Product::class)->findAll();
            $products= $paginator->paginate(
                $products,
                $request->query->getInt('page',1),
                4
            );
        }

        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $products,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/boutique/nos_produits/{slug}', name: 'app_single_product')]
    public function singleProduct($slug, PaginatorInterface $paginator,Request $request)
    {
        $product =  $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        if(!$product){
            $product= $paginator->paginate(
                $product,
                $request->query->getInt('page',1),
                4
            );
            return $this->redirectToRoute('app_shop', [
                'products' => $product,
            ]);
        }
        return $this->render('frontoffice/shop_catalog.html.twig', [
            'products' => $product,
        ]);
    }






         /**
     * @Route ("/searchclasse",name="searchclasse")
     * @param Request $request
     */

    public function searchclasse(Request $request)
    {
        
        $em=$this->getDoctrine()->getManager();


    $conn = mysqli_connect("localhost", "root", "", "scoop_feraga_database");

        
$sql = "SELECT * FROM product where category_id =".$request->request->get('name')."";
        
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result)>0){
while ($row=mysqli_fetch_assoc($result)){
    print_r(' 
    

       <div class="col-12 col-sm-6 col-lg-4 prod '.$row['id'].'" id="'.$row['id'].'">
                          <div class="__item">
                            <figure class="__image">
                              <img
                                class="lazy"
                                width="188"
                                src="{{ asset(\'uploads/images/\'~product.product_image) }}"
                                data-src="{{ asset(\'uploads/images/\'~product.product_image) }} "
                                alt="demo"
                              />
                            </figure>

                            <div class="__content">
                              <h4 class="h6 __title">
                                <a href="{{ path(\'app_single_product\',{\'slug\':product.slug}) }}">{{ product.product_name }}</a>
                              </h4>

                              <div class="__category">
                                <a href="#">'.$row['id'].'</a>
                              </div>
                              <div class="id_category">
                                <a href="#">{{ product.id}} </a>
                              </div>

                              <div class="product-price">
                                <span
                                  class="product-price__item product-price__item--new"
                                  > {{ (product.product_price / 100)  }} FCFA</span
                                >
                              </div>

                              <a
                                class="custom-btn custom-btn--medium custom-btn--style-1"
                                href="{{ path(\'app_add_to_cart\',{\'slug\': product.slug}) }}"
                                ><i class="fontello-shopping-bag"></i>Ajouter
                                panier</a
                              >
                              <a
                                style="display: block"
                                href="{{ path(\'app_single_product\',{\'slug\':product.slug}) }}"
                                >voir plus</a
                              >
                            </div>

                            <span class="product-label product-label--sale"
                              >Sale</span
                            >
                          </div>
                        </div>
    
');

}
}
else{
    echo "<tr><td> 0 result found</td></tr>";
}
return new Response('success');
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
