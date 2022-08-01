<?php

namespace App\Controller;

use App\Entity\AddressLivraison;
use App\Entity\Order;
use App\Form\AddressLivraisonType;
use App\Services\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
    public function addAddress(Request $request, Cart $cart)
    {
        $address = new AddressLivraison();

        $form = $this->createForm(AddressLivraisonType::class,$address);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            if($cart->get()){
                return $this->redirectToRoute('app_checkout');
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

}
