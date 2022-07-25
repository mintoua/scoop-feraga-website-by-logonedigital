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
    public function blog(PostsRepository $repository , PostCategoryRepository $categoryRepository , Request $request): Response
    {
        $posts = $repository->findAll();
        $cat = $request->get("catId");
        if( !$cat ==null){
            $posts = $repository->findBy(['postCategory'=>$cat]);
        }
        // on verifie si on a un requette ajax ou non
        if($request->get("ajax")){
        return new JsonResponse([
        "content" =>  $this->renderView('frontoffice/blogList.html.twig',[
                'posts'=>$posts,
            ])

]);
        }
        return $this->render('frontoffice/blog.html.twig',[
            'posts'=>$posts,
            'category'=>$categoryRepository->findAll()
            ]
        );
    }


}
