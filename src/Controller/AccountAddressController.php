<?php

namespace App\Controller;

use App\Entity\AddressLivraison;
use App\Form\AddressLivraisonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    #[Route('/compte/address', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account_address/index.html.twig');
    }

    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function addAddress()
    {
        $address = new AddressLivraison();

        $form = $this->createForm(AddressLivraisonType::class,$address);
        return $this->render('account_address/address_add.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
