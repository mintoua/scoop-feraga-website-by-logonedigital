<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Posts;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use App\Repository\LikesRepository;
use App\Entity\Likes;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\DBAL\Types\TextType;
use MercurySeries\FlashyBundle\FlashyNotifier;
use PhpParser\Node\Stmt\Label;
use Snipe\BanBuilder\CensorWords;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use App\Services\CurlService;

class BlogController extends AbstractController
{

    
    #[Route('/Blog_filter/{id}', name: 'Blog_filter')]
    public function filter($id, PostsRepository $repository, PostCategoryRepository $categoryRepository): Response
    {
        if ($id == 0) {
            $posts = $repository->findAll();
        } else {
            $posts = $repository->findBy(['postCategory' => $id]);
        }
        return $this->render('frontoffice/blog.html.twig', [
                'posts' => $posts,
                'category' => $categoryRepository->findAll()
            ]
        );
    }

    #[Route('/blog_details/{slug}', name: 'blog_details')]
    public function blog_details( Posts $post,SeoPageInterface $seoPage,CurlService $client,CommentaireRepository $commentairerepository,PostsRepository $postsRepository,LikesRepository $likesRepository ,Request $request,$slug,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        //Referencement
        $seoPage->setTitle($slug)
            ->addMeta('property','og:title',$slug)
            ->addMeta('property','og:type','blog')
            ->addMeta('name', 'description', $post->getDescription())
            ->addMeta('property', 'og:description', $post->getDescription());

        // to display comments related to blog
        $comments = $commentairerepository->findByBlog($post->getId());
        // partie creation comment
        if($this->getUser()){
            $em = $this->getDoctrine()->getManager();
            $postId = $request->get("postId");
            //on verifi si il ya une requette ajax ( on a un paramettre ajax ,is ajax = 1 alors on a une req ajax)
            if($request->get("ajax") == 1){
                // on verifi si l'utilisateur deja liker ce post ou non
                if ($likesRepository->isLiked($postId ,$this->getUser()->getId()) == []){
                    // il s'agit d'une nouveau like
                    $like = new Likes();
                    $like->setUser($this->getUser());
                    $like->setPost($post);
                    $em->persist($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" =>  $this->renderView('frontoffice/totalLike.html.twig',[
                            'totalLikes'=>$likesRepository->likesParPost($postId)
                        ])

                    ]);
                }else{
                    //l'utilisateur est déja liké ce post donc on delete le like
                    $like =  $likesRepository->find($likesRepository->isLiked($postId ,$this->getUser()->getId())[0]);
                    $em->remove($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" =>  $this->renderView('frontoffice/totalLike.html.twig',[
                            'totalLikes'=>$likesRepository->likesParPost($postId)
                        ])

                    ]);

                }
            }
            // lors de chargement du page on verifi si l'utlisateur liker cette post ou non
            $liked = 0;
            if(! $likesRepository->isLiked($post->getId() ,$this->getUser()->getId()) == []){
                $liked = 1;
            }
            return $this->render('frontoffice/blog_details.html.twig',[
                    'post'=>[$post] ,
                    'category'=>$categoryRepository->findAll(),
                    'totalLikes'=>$likesRepository->likesParPost($post->getId()),
                    'isLiked'=> $liked,
                    'comments' => $comments,
                    'id' => $post->getId(),
                ]
            );
        }

        return $this->render('frontoffice/blog_details.html.twig',[
                'post'=>$repository->findBy(['slug'=>$slug]) ,
                'category'=>$categoryRepository->findAll(),
                'totalLikes'=>$likesRepository->likesParPost($repository->findBy(['slug' => $slug])[0]->getId()),
                'isLiked'=>false,
                'comments' => $comments,
                'id' => $repository->findBy(['slug' => $slug])[0]->getId(),
            ]
        );
        }
    #[Route('/blog_details/addComment/{slug}', name: 'addComment')]
    public function addComment(CurlService $client, CommentaireRepository $commentaireRepository,Request $request,Posts $post, PostsRepository $repository, PostCategoryRepository $categoryRepository): Response
    {
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$request->get('gtoken')}";
        $response = $client->curlManager($url);
        if (!empty($response) || !is_null($response)) {
            if ($request->get("message") != null && $this->isCsrfTokenValid('comment', $request->get("_token"))) {
                $commentaire = new Commentaire();
                $user = $this->getUser();
                $commentaire->setUser($user);
                $commentaire->setBlog($post);
                $commentaire->setMessage($request->get("message"));
                $em = $this->getDoctrine()->getManager();
                $em->persist($commentaire);
                $em->flush();
                //return la nouveau list des commentaires en format HTML
                return new JsonResponse([
                    "content" => $this->renderView('frontoffice/CommentList.html.twig', [
                        'comments' => $commentaireRepository->findByBlog($post),
                    ])
                ]);
            }
        }
    }
    
    #[Route('/blog_details/updateComment/{id}', name: 'updateComment')]
    public function updateComment( FlashyNotifier $flashy,CurlService $client,CommentaireRepository $commentaireRepository,Request $request,Commentaire $commentaire, PostsRepository $postsRepository, PostCategoryRepository $categoryRepository): Response
    {
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$request->get('gtoken')}";
        $response = $client->curlManager($url);
        if (!empty($response) || !is_null($response)) {
            $this->denyAccessUnlessGranted('EDIT', $commentaire);
            if ($request->get("message") != null && $this->isCsrfTokenValid('updateCommentaire', $request->get("_token"))) {
                $commentaire->setMessage($request->get("message"));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $post = $postsRepository->findBy(['slug' => $commentaire->getBlog()->getSlug()]);
                //return la nouveau list des commentaires en format HTML
                return new JsonResponse([
                    "content" => $this->renderView('frontoffice/CommentList.html.twig', [
                        'comments' => $commentaireRepository->findByBlog($post),
                    ])
                ]);
            }else{
                 die();
            }
        }else{
            die();
        }
    }
    #[Route('/blog_details/deleteComment/{id}', name: 'deleteComment')]
    public function deleteComment(CommentaireRepository $commentaireRepository,Request $request,Commentaire $commentaire, PostsRepository $postsRepository, PostCategoryRepository $categoryRepository): Response
    {
        $this->denyAccessUnlessGranted('DELETE',$commentaire);
        if ( $this->isCsrfTokenValid('delete', $request->get("_token"))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaire);
            $em->flush();
            $post = $postsRepository->findBy(['slug' =>$commentaire->getBlog()->getSlug()]);
            //return la nouveau list des commentaires en format HTML
            return new JsonResponse([
                "content" =>  $this->renderView('frontoffice/CommentList.html.twig',[
                    'comments' => $commentaireRepository->findByBlog($post),
                ])
            ]);
        }else{
            die();
        }
        //   if ($request->isMethod('post')) {
        //       $contenu = $request->request->get("message");
        //       $name = $request->request->get("name");
        //       $email = $request->request->get("email");
        //       $comment1->setMessage($contenu);
        //       $comment1->setName($name);
        //       $comment1->setEmail($email);
        //       $entityManager = $this->getDoctrine()->getManager();
        //       $entityManager->persist($comment1);
        //       $entityManager->flush();
        //   }
        return $this->render('frontoffice/blog_details.html.twig', [
                'post' => $repository->findBy(['id' => $id]),
                'category' => $categoryRepository->findAll(),
                'comments' => $comments,
                // 'form' => $form->createView(),
                'id' => $article[0]->getId(),

            ]
        );

    }
}
