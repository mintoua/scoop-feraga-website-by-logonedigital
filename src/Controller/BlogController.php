<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use Snipe\BanBuilder\CensorWords;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function blog_details(Request $request,$id,PostsRepository $repository , PostCategoryRepository $categoryRepository , CommentaireRepository $commentairerepository): Response
    {
        $comments = $commentairerepository->findByBlog($id);
        $comment1 = new Commentaire();
        $article=$repository->findBy(['id'=>$id]);
        //    $user = $this->getUser();
          //$comment1->setUserid($user);
        //$comment1->setBlogId($article[0]);
        $form = $this->createForm(CommentaireType::class,$comment1 );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contenuComment = $form->getData()->getMessage();
          // $censor = new CensorWords;
          // $badwords = $censor->setDictionary('fr');
       //     $cleanedComment = $censor->censorString($contenuComment);
            $comment1->setMessage($contenuComment);
            $comment1->setName($form->getData()->getName());
            $comment1->setEmail($form->getData()->getEmail());

           // echo($comment1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment1);
            $entityManager->flush();

            return $this->render('frontoffice/blog_details.html.twig',[
                    'post'=>$repository->findBy(['id'=>$id]) ,
                    'category'=>$categoryRepository->findAll(),
                    'comments'=> $comments,
                    'form' => $form->createView(),

                ]
            );        }

        return $this->render('frontoffice/blog_details.html.twig',[
                'post'=>$repository->findBy(['id'=>$id]) ,
                'category'=>$categoryRepository->findAll(),
                'comments'=> $comments,
                'form' => $form->createView(),

            ]
        );
    }
}
