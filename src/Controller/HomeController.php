<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\Product;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class HomeController extends AbstractController
{
    private $entityManager;
    private $cache;

    public function __construct(
     EntityManagerInterface $entityManager,
     CacheInterface $cache,
     private PostsRepository $postRepo,
     private UrlGeneratorInterface $urlGenerator,
     private SeoPageInterface $seoPage
     ){
        $this->entityManager = $entityManager;
        $this->cache =$cache;
    }

    #[Route('/mentions-legales', name:'app_private_policy')]
    public function mensionLegage(){
        return $this->render('frontoffice/mension-legale.html.twig');
    }

    #[Route('/', name: 'app_home')]
    public function index(Mail $sender): Response
    {
        $products = $this -> cache -> get ( 'product_best_list' , function ( ItemInterface $item ) {
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $this->entityManager->getRepository (Product::class)->findByIsBest(1);
        });

       
        $posts = $this->cache->get('post_home', function(ItemInterface $item){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $this->postRepo->findByPost();
        });

        

        //bloc seo
        $description="Scoops Ferega est une ferme intégrée mis sur pied pour augmenter la production des ressources alimentaires en utilisant peu de moyens externes. Son atout est que ses activités se regroupent et sont interdépendantes : l’élevage et l'agriculture se complètent afin de diversifier la nourriture et d'améliorer les capacités nutritionnelles.";
        $this->seoPage->setTitle("la meilleure ferme intégrée du Cameroun")
            ->addMeta('name', 'description', $description)
            ->addMeta('name', 'keywords', "ferme intégrée, nutrition animal, cameroun, ferme songaï")
            ->addMeta('property', 'og:title', "ferme intégrée Cameroun - scoops feraga")
            ->setLinkCanonical($this->urlGenerator->generate('app_home',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_home',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Accueil', []);

      
        return $this->render('frontoffice/index.html.twig', [
            'posts'=>$posts,
            'products'=>$products
        ]);
    }

    #[Route('/scoops-feraga', name: 'app_about')]
    public function a_propos(): Response
    {   
        $description="Scoops Ferega est une ferme intégrée basé au Cameroun dans la ville de Yaoundé depuis quelques années. 
        Son but principal est de produire une quantité plus que suffisante des matières premières de ressource alimentaire, 
        afin d’assurer la bonne alimentation, la reproduction, 
        le survis des animaux et des populations du Cameroun voire même de toute l’Afrique.";
        $this->seoPage->setTitle("présentation de la ferme intégrée scoops Ferega")
            ->addMeta('name', 'description', $description)
            ->addMeta('name', 'keywords', "ferme intégrée, nutrition animale, cameroun, yaoundé, agriculture, élévage")
            ->addMeta('property', 'og:title', "présentation de la ferme intégrée scoops Ferega")
            ->setLinkCanonical($this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_about',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('A propos', []);
        return $this->render('frontoffice/about.html.twig');
    }


    #[Route('/nos-actualites', name: 'app_blog')]
    public function blog(CacheInterface $cache, PostsRepository $repository, PostCategoryRepository $categoryRepository, Request $request): Response
    {
        $cat = $request->get("catSlug", 'Tous');
        // on definie le nombre d'element par page
        $limit = 6;
        // o n recupere le num de la page
        $page = (int)$request->query->get("page", 1);
        //NEW
        $postsP = $postsP = $cache->get('post_list', function (ItemInterface $item) use ($repository) {
            $item->expiresAfter(DateInterval::createFromDateString('48 hour'));
            return $repository->getAllPostesOrdred();
        });
        $result = [];
        foreach ($postsP as $key => $value) {
            if ($key <= ($limit * $page) - 1 and $key >= ($limit * $page) - $limit) {
                array_push($result, $value);
            }
        }
        if ($cat != 'Tous') {
            $result = [];
            $res = [];
            $categorySelectedId = $categoryRepository->findBySlug($cat)[0]->getId();
            foreach ($postsP as $key => $value) {
                if ($value->getPostCategory()->getId() == $categorySelectedId) {
                    array_push($res, $value);
                }
            }
            foreach ($res as $key => $value) {
                if ($key <= ($limit * $page) - 1 and $key >= ($limit * $page) - $limit) {
                    array_push($result, $value);
                }
            }
            $total = count($res);
        } else {
            $total = count($postsP);
        }

        $category = $cache->get('category_list', function (ItemInterface $item) use ($categoryRepository) {
            //$item->expiresAfter(30);
            return $categoryRepository->findAll();
        });
        // on verifie si on a un requette ajax ou non
        if ($request->get("ajax")) {
            return new JsonResponse([
                "content" => $this->renderView('frontoffice/blogList.html.twig', [
                    'posts' => $result,
                    'total' => $total,
                    'category' => $category,
                    'limit' => $limit,
                    'page' => $page

                ])

            ]);
        }
        $description = "Offrir le meilleur de la nature est la source de vitalité de scoops Ferega. 
        Pour vous faire découvrir la magie que nous opérons lorsque nous travaillons et sur la valeur de nos cultures, 
        nous allons vous présenter continuellement quelques astuces et conseils sur l’élevage des animaux et la culture maraichers. 
        Ceci dans nos articles spécialement conçus pour vous.";

        $this->seoPage->setTitle("toutes les actualités liées à la ferme intégrée scoops feraga")
            ->addMeta('name', 'description', $description)
            ->addMeta('name', 'keywords', "ferme intégrée, nutrition animale, cameroun, yaoundé, agriculture, élévage, poulet, maïs, soja, animaux, provende, nutrition animal")
            ->addMeta('property', 'og:title', "toutes les actualités liées à la ferme intégrée scoops feraga")
            ->setLinkCanonical($this->urlGenerator->generate('app_blog',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_blog',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('Nos actualités', []);
        return $this->render('frontoffice/blog.html.twig', [
                'posts' => $result,
                'category' => $category,
                'total' => $total,
                'limit' => $limit,
                'page' => $page
            ]
        );
    }


}
