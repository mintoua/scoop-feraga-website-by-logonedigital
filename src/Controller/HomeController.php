<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }

    #[Route('/a_propos', name: 'app_about')]
    public function a_propos(): Response
    {
        return $this->render('frontoffice/about.html.twig');
    }

    // #[Route('/vie_a_la_ferme', name: 'app_activities')]
    // public function vie_a_la_ferme(): Response
    // {
    //     return $this->render('frontoffice/activities.html.twig');
    // }

    #[Route('/nos_actualités', name: 'app_blog')]
    public function blog(): Response
    {
        return $this->render('frontoffice/blog.html.twig');
    }

    #[Route('/contacts', name: 'app_contacts')]
    public function contacts(): Response
    {
        return $this->render('frontoffice/contacts.html.twig');
    }

    #[Route('/panier', name: 'app_cart')]
    public function panier(): Response
    {
        return $this->render('frontoffice/cart.html.twig');
    }

    #[Route('/sign-in', name: 'sign_in')]
    public function signIn(): Response
    {
        return $this->render('frontoffice/checkout.html.twig');
    }
}
