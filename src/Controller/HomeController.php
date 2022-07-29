<?php

namespace App\Controller;

use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Classes\Mail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Clue\StreamFilter\fun;

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
    public function blog( CacheInterface $cache,PostsRepository $repository , PostCategoryRepository $categoryRepository , Request $request): Response
    {
        $cat = $request->get("catId");
        // on definie le nombre d'element par page
        $limit = 2;
        // o n recupere le num de la page
        $page = (int)$request->query->get("page",1);
        // Return tous les posts par page
        if (($page == 1 and $cat == null) or ($page == 1 and $cat == 0)) {
            $postsP = $cache->get('post_list', function (ItemInterface $item) use ($repository, $page, $limit, $cat) {
                $item->expiresAfter(30);
                return $repository->getPaginatedPosts($page, $limit, null);
            });
        }else{
            $postsP=$repository->getPaginatedPosts($page, $limit, $cat);
        }
            //$repository->getPaginatedPosts($page , $limit , $cat);
        //on recupere le nombre totale du postsif
        if (($page == 1 and $cat == null) or ($page == 1 and $cat == 0)) {
            $total = $cache->get('total_post', function (ItemInterface $item) use ($repository, $cat) {
                $item->expiresAfter(30);
                return $repository->getTotalPosts($cat);
            });
        }else{
            $total  = $repository->getTotalPosts($cat);
        }
        $category = $cache->get('category_list',function (ItemInterface $item) use($categoryRepository){
            $item->expiresAfter(30);
            return $categoryRepository->findAll();
        });




       // $posts = $repository->findAll();

       /* if( !$cat ==null){
            $posts = $repository->findBy(['postCategory'=>$cat]);
        }*/
        // on verifie si on a un requette ajax ou non
        if($request->get("ajax")){
        return new JsonResponse([
        "content" =>  $this->renderView('frontoffice/blogList.html.twig',[
                'posts'=>$postsP,
            'total' => $total,
            'limit'=> $limit,
            'page' => $page

            ])

]);
        }
        return $this->render('frontoffice/blog.html.twig',[
            'posts'=>$postsP,
            'category'=>$category,
                'total' => $total,
                'limit'=> $limit,
                'page' => $page
            ]
        );
    }


}
