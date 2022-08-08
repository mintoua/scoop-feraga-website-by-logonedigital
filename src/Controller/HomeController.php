<?php

namespace App\Controller;


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
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class HomeController extends AbstractController
{
    private $entityManager;
    private $cache;

    public function __construct(EntityManagerInterface $entityManager, CacheInterface $cache){
        $this->entityManager = $entityManager;
        $this->cache =$cache;
    }
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $products = $this -> cache -> get ( 'product_best_list' , function ( ItemInterface $item ) {
            $item -> expiresAfter ( 3600 );
            return $this->entityManager->getRepository (Product::class)->findByIsBest(1);
        } );

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
    public function blog(SeoPageInterface $seoPage, CacheInterface $cache, PostsRepository $repository, PostCategoryRepository $categoryRepository, Request $request): Response
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
        $seoPage->setTitle("Blogs")
            ->addMeta('property', 'og:title', 'blogs')
            ->addMeta('property', 'og:type', 'blog');
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
