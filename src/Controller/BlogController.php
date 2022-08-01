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
    public function blog_details( SeoPageInterface $seoPage,CurlService $client,CommentaireRepository $commentairerepository,PostsRepository $postsRepository,LikesRepository $likesRepository ,Request $request,$slug,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        $post = $repository->findBy(['slug'=>$slug])[0];
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
            if($request->get("ajax") == 1){
                if ($likesRepository->isLiked($postId ,$this->getUser()->getId()) == []){
                    $like = new Likes();
                    $like->setUser($this->getUser());
                    $like->setPost($postsRepository->findBy(['id'=>$postId])[0]);
                    $em->persist($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" =>  $this->renderView('frontoffice/totalLike.html.twig',[
                            'totalLikes'=>$likesRepository->likesParPost($postId)
                        ])

                    ]);
                }else{
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
    public function addComment( CommentaireRepository $commentaireRepository,Request $request,Posts $post, PostsRepository $repository, PostCategoryRepository $categoryRepository): Response
    {
        if ($request->get("message") != null && $this->isCsrfTokenValid('comment', $request->get("_token"))){
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
                "content" =>  $this->renderView('frontoffice/CommentList.html.twig',[
                    'comments' => $commentaireRepository->findByBlog($post),
                ])
            ]);
        }
    }
}
