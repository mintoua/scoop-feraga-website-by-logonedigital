<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
     $this->em=$em;   
    }
   

    #[Route('/s-inscire-lgo', name: 'app_register')]
    public function registerUser(
        EntityManagerInterface $em, 
        Request $req, 
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {   
        $user = new User();
        $form =$this->createForm(UserType::class, $user);
        $form->handleRequest($req);

        
        if($form->isSubmitted() && $form->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_inscire');
        }

        return $this->render('frontoffice/s-inscire.html.twig', [
            'form'=>$form->createView()
        ]);
    }


    #[Route('/se-connecter', name:'app_sign_in')]
    public function seConnecter(){

        return $this->render('frontoffice/sign_in.html.twig');
        
    }

    #[Route(path:"/mon-compte", name:"app_user_account")]
    public function account(){
        return $this->render("frontoffice/account.html.twig");
    }

    #[Route(path:"/mon-compte/mofier-mon-mot-de-passe", name:'app_user_password')]
    public function accountChangePassword(
        Request $req, 
        UserPasswordHasherInterface $passwordHasher,
        FlashyNotifier $flashy){
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $old_password = $form->get('old_password')->getData();
            if($passwordHasher->isPasswordValid($user, $old_password)){
                $new_password = $form->get('new_password')->getData();
                
                $hashedPassword = $passwordHasher->hashPassword($user, $new_password);
                $user->setPassword($hashedPassword);

                $this->em->flush();
                $flashy->success('Votre mot de passe à bien été modifier !');
            }else{
                $flashy->error("Mot de passe incorrect !");
            }
        }

        return $this->render('frontoffice/account-infos.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
