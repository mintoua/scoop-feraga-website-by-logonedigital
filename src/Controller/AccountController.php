<?php

namespace App\Controller;

use App\Entity\AddressLivraison;
use App\Entity\Comments;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\AddressLivraisonType;
use App\Services\BoutiqueService;
use App\Services\Cart;
use App\Services\CurlService;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


class AccountController extends AbstractController
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    //
    //ALL ABOUT THE USER ADDRESSES
    //

    #[Route('/mon-compte/address', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/addresses.html.twig');
    }

    #[Route('/mon-compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function addAddress(Request $request, CurlService $client, Cart $cart)
    {
        $address = new AddressLivraison();

        $form = $this->createForm(AddressLivraisonType::class,$address);

        $form->add("captcha", HiddenType::class, [
            "constraints"=>[
                new NotNull(),
                new NotBlank()
            ]
        ]);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$form->get('captcha')->getData()}";

            $response = $client->curlManager($url);

            if(empty($response) || is_null($response)){
                return $this->redirectToRoute('app_account_address_add');
            }else{
                $data = json_decode($response);
                if($data->success){
                    $address->setUser($this->getUser());
                    $this->entityManager->persist($address);
                    $this->entityManager->flush();
                    if($cart->get()){
                        return $this->redirectToRoute('app_checkout');
                    }
                }else{
                    dd("error");
                    return $this->redirectToRoute('app_account_address_add');
                }
            }

            return $this->redirectToRoute('app_account_address');
        }
        return $this->render('account/address_form.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/mon-compte/modifier-une-adresse/{id}', name: 'app_account_address_edit')]
    public function editAddress(Request $request, $id)
    {
        $address = $this->entityManager->getRepository(AddressLivraison::class)->findOneById($id);
       // dd($address);
        if(!$address || $address->getUser() != $this->getUser() ){
            return $this->redirectToRoute('app_account_address');
        }

        $form = $this->createForm(AddressLivraisonType::class,$address);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->flush();

            return $this->redirectToRoute('app_account_address');
        }
        return $this->render('account/address_form.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/mon-compte/supprimer-une-adresse/{id}', name: 'app_account_address_delete')]
    public function deleteAddress($id)
    {
        $address = $this->entityManager->getRepository(AddressLivraison::class)->findOneById($id);

        if($address && $address->getUser() == $this->getUser() ){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }
            return $this->redirectToRoute('app_account_address');
    }

    //
    // All About The User Order
    //

    #[Route('/mon-compte/mes-commandes', name: 'app_account_orders')]
    public function userOrder()
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());

        return $this->render('account/orders.html.twig',[
            'orders'=>$orders
        ]);
    }

    #[Route('/mon-compte/mes-commandes/{reference}', name: 'app_account_orders_show')]
    public function userDetailedOrder($reference)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account_orders');
        }
        return $this->render('account/orders_show.html.twig',[
            'order'=>$order
        ]);
    }

    #[Route('/mon-compte/commandes/avis/{slug}', name: 'app_account_add_review')]
    public function addReviewProduct($slug, Request $request, BoutiqueService $service){
        $message = $request->get("message");
        $rating = $request->get("rating");

        if( $message != null){
            $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
            $service->persistComment($message,$rating,$this->getUser(),$product);
            $comments = $this->entityManager->getRepository(Comments::class)->findComments($product);

            return new JsonResponse([
                "content" =>  $this->renderView('frontoffice/comments_list.html.twig',[
                    'product' => $product,
                    'comments' => $service->toPaginate($comments,$request,10),
                ])
            ]);
        }
    }

}
