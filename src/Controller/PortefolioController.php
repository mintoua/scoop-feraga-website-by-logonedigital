<?php

namespace App\Controller;

use App\Repository\CategoryPictureRepository;
use App\Repository\FarmPicturesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortefolioController extends AbstractController
{
    #[Route('/vie_a_la_ferme', name: 'app_activities')]
    public function index(CategoryPictureRepository $categoryPictureRepo, FarmPicturesRepository $farmPictureRepo): Response
    {   

        return $this->render('frontoffice/activities.html.twig', [
            'categoriesPictures' => $categoryPictureRepo->findAll(),
            'farmPictures' => $farmPictureRepo->findAll()
        ]);
    }
}
