<?php

namespace App\Controller;

use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use App\Repository\LikesRepository;
use App\Entity\Likes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function blog_details( PostsRepository $postsRepository,LikesRepository $likesRepository ,Request $request,$id,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $postId = $request->get("postId");
            if($request->get("ajax")){
                if ($likesRepository->isLiked($postId ,$this->getUser()->getId()) == []){
                    $like = new Likes();
                    $like->setUser($this->getUser());
                    $like->setPost($postsRepository->findBy(['id'=>$postId])[0]);
                    $em->persist($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" =>  $this->renderView('frontoffice/like.html.twig',[
                            'totalLikes'=>$likesRepository->likesParPost($postId)
                        ])

                    ]);
                }else{
                    $like =  $likesRepository->find($likesRepository->isLiked($postId ,$this->getUser()->getId())[0]);
                    $em->remove($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" =>  $this->renderView('frontoffice/dislike.html.twig',[
                            'totalLikes'=>$likesRepository->likesParPost($postId)
                        ])

                    ]);

                }
            }
            $liked = false;
            if(! $likesRepository->isLiked($id ,$this->getUser()->getId()) == []){
                $liked =! $liked;
            }
            return $this->render('frontoffice/blog_details.html.twig',[
                    'post'=>$repository->findBy(['id'=>$id]) ,
                    'category'=>$categoryRepository->findAll(),
                    'totalLikes'=>$likesRepository->likesParPost($id),
                    'isLiked'=>$liked
                ]
            );
        }
        return $this->render('frontoffice/blog_details.html.twig',[
                'post'=>$repository->findBy(['id'=>$id]) ,
                'category'=>$categoryRepository->findAll(),
                'totalLikes'=>$likesRepository->likesParPost($id),
                'isLiked'=>false
            ]
        );
        }

}
