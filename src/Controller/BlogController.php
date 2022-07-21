<?php

namespace App\Controller;

use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/Blog_filter/{id}', name: 'Blog_filter')]
    public function filter($id,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        if($id == 0 ){
          $posts = $repository->findAll();
        }else{
            $posts = $repository->findBy(['postCategory'=>$id]);
        }
        return $this->render('frontoffice/blog.html.twig',[
                'posts'=>$posts ,
                'category'=>$categoryRepository->findAll()
            ]
        );
    }

    #[Route('/blog_details/{id}', name: 'blog_details')]
    public function blog_details($id,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        return $this->render('frontoffice/blog_details.html.twig',[
                'post'=>$repository->findBy(['id'=>$id]) ,
                'category'=>$categoryRepository->findAll()
            ]
        );
    }
}
