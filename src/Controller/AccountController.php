<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use App\Entity\Product;
use App\Entity\Comments;
use App\Services\CurlService;
use App\Entity\AddressLivraison;
use App\Form\ChangePasswordType;
use App\Services\BoutiqueService;
use App\Form\AddressLivraisonType;
use Flasher\Prime\FlasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class AccountController extends AbstractController
{
    private $entityManager;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct ( EntityManagerInterface $entityManager , private AuthorizationCheckerInterface $authChecker,
                                  private FlasherInterface $flasher
    )
    {
        $this -> entityManager = $entityManager;
    }

    #[Route( path : "/mon-compte" , name : "app_user_account" )]
    public function account ( FlasherInterface $flasher )
    {
        if ( $this -> authChecker -> isGranted ( 'ROLE_ADMIN' ) ) {
            return $this -> redirectToRoute ( 'admin' );
        }

        $user = $this -> getUser ();
        if ( $user -> isBlocked () ) {
            $flasher -> addError ( 'Votre compte a été bloqué par les administrateurs.' );
            return $this -> redirectToRoute ( 'app_home' );
        } else if ( !$user -> isIsVirified () ) {
            $flasher -> addWarning ( "Veuillez confirmez votre email avant d'accéder à votre compte." );
            return $this -> redirectToRoute ( 'app_home' );
        }
        return $this -> render ( "frontoffice/account.html.twig" );
    }

    #[Route( path : "/mon-compte/mofier-mon-mot-de-passe" , name : 'app_user_password' )]
    public function accountChangePassword (
        Request $req,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $user = $this->getUser();
        $form = $this->createForm( ChangePasswordType::class , $user );

        $form -> handleRequest ( $req );
       
        if ( $form -> isSubmitted () ) {
            //dd ( $form );
            $old_password = $form -> get ( 'old_password' ) -> getData ();
            if ( $passwordHasher -> isPasswordValid ( $user , $old_password ) ) {
                $new_password = $form -> get ( 'new_password' ) -> getData ();

                $hashedPassword = $passwordHasher -> hashPassword ( $user , $new_password );
                $user -> setPassword ( $hashedPassword );

                $this -> entityManager -> flush ();

                $this -> flasher -> addSuccess ( 'Votre mot de passe à bien été modifier !' );
                return $this -> redirect ( $req -> headers -> get ( 'referer' ) );
            } else {
                //dd ( $this -> flasher );
                $this -> flasher -> addError ( "le mot de passe que vous avez entré </br> ne 
                                        correspont pas à votre ancien mot de passe. </br>
                                        Si l'avez oublié dans cas vous devez faire une procédure de mot de passe oublié.
                                        " );
                return $this -> redirectToRoute ( 'app_user_password' );
            }
        }

        return $this -> render ( 'frontoffice/account-infos.html.twig' , [
            'form' => $form -> createView ()
        ] );
    }


    #[Route( '/mon-compte/address' , name : 'app_account_address' )]
    public function index () : Response
    {
        return $this -> render ( 'account/addresses.html.twig' );
    }

    #[Route( '/mon-compte/ajouter-une-adresse' , name : 'app_account_address_add' )]
    public function addAddress ( Request $request , CurlService $client , Cart $cart )
    {
        $address = new AddressLivraison();

        $form = $this -> createForm ( AddressLivraisonType::class , $address );

        $form -> add ( "captcha" , HiddenType::class , [
            "constraints" => [
                new NotNull() ,
                new NotBlank()
            ]
        ] );

        $form -> handleRequest ( $request );


        if ( $form -> isSubmitted () && $form -> isValid () ) {
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$form->get('captcha')->getData()}";

            $response = $client -> curlManager ( $url );

            if ( empty( $response ) || is_null ( $response ) ) {
                $this->flasher->addWarning("Votre demande n'a pas pu être envoyé.");
                return $this -> redirectToRoute ( 'app_account_address_add' );
            } else {
                $data = json_decode ( $response );
                if ( $data -> success ) {
                    $address -> setUser ( $this -> getUser () );
                    $this -> entityManager -> persist ( $address );
                    $this -> entityManager -> flush ();
                    if ( $cart -> get () ) {
                        $this->flasher->addSuccess("Adresse ajoutée");
                        return $this -> redirectToRoute ( 'app_checkout' );
                    }
                } else {
                    $this->flasher->addError("Error! Veuillez vérifier les champs!");
                    return $this -> redirectToRoute ( 'app_account_address_add' );
                }
            }

            return $this -> redirectToRoute ( 'app_account_address' );
        }

        return $this -> render ( 'account/address_form.html.twig' , [
            'form' => $form -> createView ()
        ] );
    }

    #[Route( '/mon-compte/modifier-une-adresse/{id}' , name : 'app_account_address_edit' )]
    public function editAddress ( Request $request , $id )
    {
        $address = $this -> entityManager -> getRepository ( AddressLivraison::class ) -> findOneById ( $id );
        // dd($address);
        if ( !$address || $address -> getUser () != $this -> getUser () ) {
            return $this -> redirectToRoute ( 'app_account_address' );
        }

        $form = $this -> createForm ( AddressLivraisonType::class , $address );
        $form -> handleRequest ( $request );
        if ( $form -> isSubmitted () && $form -> isValid () ) {

            $this -> entityManager -> flush ();

            return $this -> redirectToRoute ( 'app_account_address' );
        }
        return $this -> render ( 'account/address_form.html.twig' , [
            'form' => $form -> createView ()
        ] );
    }

    #[Route( '/mon-compte/supprimer-une-adresse/{id}' , name : 'app_account_address_delete' )]
    public function deleteAddress ( $id )
    {
        $address = $this -> entityManager -> getRepository ( AddressLivraison::class ) -> findOneById ( $id );

        if ( $address && $address -> getUser () == $this -> getUser () ) {
            $this -> entityManager -> remove ( $address );
            $this -> entityManager -> flush ();
        }
        return $this -> redirectToRoute ( 'app_account_address' );
    }

    //
    // All About The User Order
    //

    #[Route( '/mon-compte/mes-commandes' , name : 'app_account_orders' )]
    public function userOrder ()
    {
        $orders = $this -> entityManager -> getRepository ( Order::class ) -> findSuccessOrders ( $this -> getUser () );

        return $this -> render ( 'account/orders.html.twig' , [
            'orders' => $orders
        ] );
    }

    #[Route( '/mon-compte/mes-commandes/{reference}' , name : 'app_account_orders_show' )]
    public function userDetailedOrder ( $reference )
    {
        $order = $this -> entityManager -> getRepository ( Order::class ) -> findOneByReference ( $reference );

        if ( !$order || $order -> getUser () != $this -> getUser () ) {
            return $this -> redirectToRoute ( 'app_account_orders' );
        }
        return $this -> render ( 'account/orders_show.html.twig' , [
            'order' => $order
        ] );
    }

    /* #[Route('/mon-compte/commandes/avis/{slug}', name: 'app_account_add_review')]
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
     }*/

}
