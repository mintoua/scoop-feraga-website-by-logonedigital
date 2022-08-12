<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\PostCategory;
use App\Entity\Posts;
use App\Repository\CommentaireRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use App\Repository\LikesRepository;
use App\Entity\Likes;
use Flasher\Prime\FlasherInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\CurlService;

class BlogController extends AbstractController
{
    public function __construct(
        private FlasherInterface $flasher,
        private PostsRepository $postsRepository,
        private PostCategoryRepository $categoryRepository,
        private CommentaireRepository $commentairerepository,
        private LikesRepository $likesRepository,
        private CurlService $client,
        private UrlGeneratorInterface $urlGenerator


    )
    {

    }

    #[Route('/Blog-filter/{slug}', name: 'Blog_filter')]
    public function filter(PostCategory $postCategory ): Response
    {
        return $this->redirect($this->generateUrl('app_blog', array('catSlug' => $postCategory->getSlug()
        )));
    }

    #[Route('/blog-details/{slug}', name: 'blog_details')]
    public function blog_details(Posts $post, SeoPageInterface $seoPage, $slug , Request $request): Response
    {
        //Referencement
        $seoPage->setTitle($slug)
            ->addMeta('property', 'og:title', $post->getSlug())
            ->addMeta('property', 'og:type', 'blog')
            ->addMeta('name', 'description', $post->getDescription())
            ->addMeta('property', 'og:description', $post->getDescription())
            ->addMeta('name', 'keywords', "ferme intégrée, nutrition animal, cameroun, ferme songaï")
            ->addMeta('property', 'og:title', $post->getSlug())
            ->addMeta('property', 'og:image', "https://127.0.0.1:8000/uploads/images//". $post->getPostImage())
            ->setLinkCanonical($this->urlGenerator->generate('blog_details',['slug'=>$slug], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('blog_details',['slug'=>$slug], urlGeneratorInterface::ABSOLUTE_URL))
            ->setBreadcrumb('blog', ["article"=>$post]);
            ;

        // to display comments related to blog
        $comments = $this->commentairerepository->findByBlog($post->getId());
        // partie creation comment
        if ($this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $postId = $request->get("postId");
            //on verifi si il ya une requette ajax ( on a un paramettre ajax ,is ajax = 1 alors on a une req ajax)
            if ($request->get("ajax") == 1) {
                // on verifi si l'utilisateur deja liker ce post ou non
                if ($this->likesRepository->isLiked($postId, $this->getUser()->getId()) == []) {
                    // il s'agit d'une nouveau like
                    $like = new Likes();
                    $like->setUser($this->getUser());
                    $like->setPost($post);
                    $em->persist($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" => $this->renderView('frontoffice/totalLike.html.twig', [
                            'totalLikes' => $this->likesRepository->likesParPost($postId)
                        ])

                    ]);
                } else {
                    //l'utilisateur est déja liké ce post donc on delete le like
                    $like = $this->likesRepository->find($this->likesRepository->isLiked($postId, $this->getUser()->getId())[0]);
                    $em->remove($like);
                    $em->flush();
                    return new JsonResponse([
                        "content" => $this->renderView('frontoffice/totalLike.html.twig', [
                            'totalLikes' => $this->likesRepository->likesParPost($postId)
                        ])

                    ]);

                }
            }
            // lors de chargement du page on verifi si l'utlisateur liker cette post ou non
            $liked = 0;
            if (!$this->likesRepository->isLiked($post->getId(), $this->getUser()->getId()) == []) {
                $liked = 1;
            }
            return $this->render('frontoffice/blog_details.html.twig', [
                    'post' => [$post],
                    'category' => $this->categoryRepository->findAll(),
                    'relatedPosts' => $this->postsRepository->getRelatedPosts($post->getPostCategory()->getId(), $post->getId()),
                    'totalLikes' => $this->likesRepository->likesParPost($post->getId()),
                    'isLiked' => $liked,
                    'comments' => $comments,
                    'id' => $post->getId(),
                ]
            );
        }

        return $this->render('frontoffice/blog_details.html.twig', [
                'post' => $this->postsRepository->findBy(['slug' => $slug]),
                'category' => $this->categoryRepository->findAll(),
                'relatedPosts' => $this->postsRepository->getRelatedPosts($post->getPostCategory()->getId(), $post->getId()),
                'totalLikes' => $this->likesRepository->likesParPost($this->postsRepository->findBy(['slug' => $slug])[0]->getId()),
                'isLiked' => false,
                'comments' => $comments,
                'id' => $this->postsRepository->findBy(['slug' => $slug])[0]->getId(),
            ]
        );
    }

    #[Route('/blog-details/addComment/{slug}', name: 'addComment')]
    public function addComment(Posts $post, Request $request): Response
    {
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$request->get('gtoken')}";
        $response = $this->client->curlManager($url);
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
                        'comments' => $this->commentairerepository->findByBlog($post),
                    ])
                ]);
            }
        }
    }

    #[Route('/blog_details/updateComment/{id}', name: 'updateComment')]
    public function updateComment(Commentaire $commentaire, Request $request): Response
    {
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$request->get('gtoken')}";
        $response = $this->client->curlManager($url);
        if (!empty($response) || !is_null($response)) {
            $this->denyAccessUnlessGranted('EDIT', $commentaire);
            if ($request->get("message") != null && $this->isCsrfTokenValid('updateCommentaire', $request->get("_token"))) {
                $commentaire->setMessage($request->get("message"));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $post = $this->postsRepository->findBy(['slug' => $commentaire->getBlog()->getSlug()]);
                //return la nouveau list des commentaires en format HTML
                return new JsonResponse([
                    "content" => $this->renderView('frontoffice/CommentList.html.twig', [
                        'comments' => $this->commentairerepository->findByBlog($post),
                    ])
                ]);
            } else {
                die();
            }
        } else {
            die();
        }
    }

    #[Route('/blog_details/deleteComment/{id}', name: 'deleteComment')]
    public function deleteComment(Commentaire $commentaire, Request $request): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $commentaire);
        if ($this->isCsrfTokenValid('delete', $request->get("_token"))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commentaire);
            $em->flush();
            $post = $this->postsRepository->findBy(['slug' => $commentaire->getBlog()->getSlug()]);
            //return la nouveau list des commentaires en format HTML
            return new JsonResponse([
                "content" => $this->renderView('frontoffice/CommentList.html.twig', [
                    'comments' => $this->commentairerepository->findByBlog($post),
                ])
            ]);
        } else {
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
