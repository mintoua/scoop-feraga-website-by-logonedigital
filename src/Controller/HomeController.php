<?php

namespace App\Controller;

use App\Entity\PostCategory;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;
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
    public function blog( SeoPageInterface $seoPage,CacheInterface $cache,PostsRepository $repository , PostCategoryRepository $categoryRepository , Request $request): Response
    {
        $cat = $request->get("catSlug" , 'Tous');
        // on definie le nombre d'element par page
        $limit = 2;
        // o n recupere le num de la page
        $page = (int)$request->query->get("page",1);
        // OLD
        // --- start ---
        /*if (($page == 1 and $cat == null) or ($page == 1 and $cat == 0)) {
            $postsP = $cache->get('post_list', function (ItemInterface $item) use ($repository, $page, $limit, $cat) {
                //$item->expiresAfter(30);
                return $repository->getPaginatedPosts($page, $limit, null);
            });
        }else{
            $postsP=$repository->getPaginatedPosts($page, $limit, $cat);
        }*/
        //$cache->getItem('post_list')->get()

            //$repository->getPaginatedPosts($page , $limit , $cat);
        //on recupere le nombre totale du postsif
        /*if (($page == 1 and $cat == null) or ($page == 1 and $cat == 0)) {
            $total = $cache->get('total_post', function (ItemInterface $item) use ($repository, $cat) {
               // $item->expiresAfter(30);
                return $repository->getTotalPosts($cat);
            });
        }else{
            $total  = $repository->getTotalPosts($cat);
        }*/
        // --- end --

        //NEW
        $postsP = $postsP = $cache->get('post_list', function (ItemInterface $item) use ($repository) {
            //$item->expiresAfter(30);
            return $repository->getAllPostesOrdred();
        });
        $result=[];
        foreach ( $postsP as $key=>$value){
            if ($key <= ($limit*$page)-1 and $key >= ($limit*$page)-$limit){
                array_push($result,$value);
            }
        }
        if($cat != 'Tous'){
            $result=[];
            $res=[];
            foreach ( $postsP as $key=>$value){
                if ($value->getPostCategory()->getId() == $categoryRepository->findBySlug($cat)[0]->getId()){
                    array_push($res,$value);
                }
            }
            foreach ( $res as $key=>$value){
                if ($key <= ($limit*$page)-1 and $key >= ($limit*$page)-$limit){
                    array_push($result,$value);
                }
            }
            $total = count($res);
        }else{
            $total = count($postsP);
        }

        $category = $cache->get('category_list',function (ItemInterface $item) use($categoryRepository){
            //$item->expiresAfter(30);
            return $categoryRepository->findAll();
        });
        // on verifie si on a un requette ajax ou non
        if($request->get("ajax")){
        return new JsonResponse([
        "content" =>  $this->renderView('frontoffice/blogList.html.twig',[
                'posts'=>$result,
            'total' => $total,
            'limit'=> $limit,
            'page' => $page

            ])

]);
        }
            $seoPage->setTitle("Blogs")
                ->addMeta('property','og:title','blogs')
                ->addMeta('property','og:type','blog');
        return $this->render('frontoffice/blog.html.twig',[
            'posts'=>$result,
            'category'=>$category,
                'total' => $total,
                'limit'=> $limit,
                'page' => $page
            ]
        );
    }


}
