<?php

namespace App\Controller;

use DateInterval;
use App\Repository\FarmPicturesRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\CategoryPictureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PortefolioController extends AbstractController
{
    private $cache;

    public function __construct(
        CacheInterface $cache,
        private SeoPageInterface $seopage,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        $this->cache = $cache;
    }

    #[Route('/vie-a-la-ferme', name: 'app_activities')]
    public function index(
    CategoryPictureRepository $categoryPictureRepo, 
    FarmPicturesRepository $farmPictureRepo,
    PaginatorInterface $paginator,
    Request $request,
    SeoPageInterface $seopage
    ): Response
    {   
        $categoriesPictures = $this->cache->get("categeroriesPictureFarm", function(ItemInterface $item) use ($categoryPictureRepo){
                $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
                return $categoryPictureRepo->findAll();
        });
        $farmPictures = $this->cache->get("farm-picture", function(ItemInterface $item) use ($paginator, $farmPictureRepo, $request){
                $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
                return $paginator->paginate($farmPictureRepo->findAll(),$request->query->getInt('page', 1),12);
        });

        //bloc seo
        $description = "Offrir le meilleur de la nature est la source de vitalité de scoops Ferega. 
        Pour vous faire découvrir la magie que nous opérons lorsque nous travaillons et sur la valeur de nos cultures, 
        nous allons vous présenter continuellement quelques astuces et conseils sur l’élevage des animaux et la culture maraichers. 
        Ceci dans nos articles spécialement conçus pour vous.";

        $seopage->setTitle("images de la ferme scoops feraga")
            ->addMeta('name', 'description', $description)
            ->addMeta('name', 'keywords', "ferme intégrée, nutrition animale, cameroun, yaoundé, agriculture, élévage, poulet, maïs, soja, animaux, provende, nutrition animal")
            ->addMeta('property', 'og:title', "images de la ferme scoops feraga")
            ->setLinkCanonical($this->urlGenerator->generate('app_activities',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_activities',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Nos actualités', []);

        return $this->render('frontoffice/activities.html.twig', [
            'categoriesPictures' => $categoriesPictures,
            'farmPictures' => $farmPictures
        ]);
    }
}
