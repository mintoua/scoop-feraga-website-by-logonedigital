<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    private $em;

    public function __construct(
    EntityManagerInterface $em,
    private FlasherInterface $flasher
    )
    {
     $this->em=$em;   
    }
   

    // #[Route('/s-inscire-lgo', name: 'app_register')]
    // public function registerUser(
    //     EntityManagerInterface $em, 
    //     Request $req, 
    //     UserPasswordHasherInterface $passwordHasher
    // ): Response
    // {   
    //     $user = new User();
    //     $form =$this->createForm(UserType::class, $user);
    //     $form->handleRequest($req);

        
    //     if($form->isSubmitted() && $form->isValid()){
    //         $user->setRoles(["ROLE_USER"]);
    //         $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
    //         $user->setPassword($hashedPassword);
    //         $em->persist($user);
    //         $em->flush();

    //         return $this->redirectToRoute('app_inscire');
    //     }

    //     return $this->render('frontoffice/s-inscire.html.twig', [
    //         'form'=>$form->createView()
    //     ]);
    // }


    #[Route('/se-connecter', name:'app_sign_in')]
    public function seConnecter(){

        return $this->render('frontoffice/sign_in.html.twig');
        
    }

}
