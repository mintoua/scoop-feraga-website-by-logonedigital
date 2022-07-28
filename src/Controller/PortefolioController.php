<?php

namespace App\Controller;

use DateInterval;
use App\Repository\FarmPicturesRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\CategoryPictureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PortefolioController extends AbstractController
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    #[Route('/vie_a_la_ferme', name: 'app_activities')]
    public function index(
    CategoryPictureRepository $categoryPictureRepo, 
    FarmPicturesRepository $farmPictureRepo,
    ): Response
    {   
        
        $categoriesPictures = $this->cache->get("categeroriesPictureFarm", function(ItemInterface $item) use ($categoryPictureRepo){
                $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
                return $categoryPictureRepo->findAll();
        });
        
        return $this->render('frontoffice/activities.html.twig', [
            'categoriesPictures' => $categoriesPictures,
            'farmPictures' => $farmPictureRepo->findAll()
        ]);
    }
}
