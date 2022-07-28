<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use App\Repository\PostCategoryRepository;
use App\Repository\PostsRepository;
use App\Repository\LikesRepository;
use App\Entity\Likes;
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

    #[Route('/slim/{id}/aa', name: 'slim')]
    public function blog_details2(FlashyNotifier $flashy, CurlService $client, Request $request, $id, PostsRepository $repository, PostCategoryRepository $categoryRepository, CommentaireRepository $commentairerepository): Response
    {
        return new JsonResponse([
            "content" => "azeazazae"]);
    }

    #[Route('/blog_details/{id}', name: 'blog_details')]
    public function blog_details( CurlService $client,CommentaireRepository $commentairerepository,PostsRepository $postsRepository,LikesRepository $likesRepository ,Request $request,$id,PostsRepository $repository , PostCategoryRepository $categoryRepository): Response
    {
        // to display comments related to blog

        $comments = $commentairerepository->findByBlog($id);
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
            if($request->get("ajax") == 2){
                $article = $repository->findBy(['id' => $id]);
                $comment1 = new Commentaire();
                $user = $this->getUser();
                $comment1->setUserid($user);
                $comment1->setBlogId($article[0]);
                $comment1->setName($request->get("name"));
                $comment1->setEmail($request->get("email"));
                $comment1->setMessage($request->get("message"));
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment1);
                $em->flush();
                return new JsonResponse([
                    "content" =>  $this->renderView('frontoffice/CommentList.html.twig',[
                        'comments' => $commentairerepository->findByBlog($id),

                    ])

                ]);
            }
            $liked = 0;
            if(! $likesRepository->isLiked($id ,$this->getUser()->getId()) == []){
                $liked = 1;
            }
            return $this->render('frontoffice/blog_details.html.twig',[
                    'post'=>$repository->findBy(['id'=>$id]) ,
                    'category'=>$categoryRepository->findAll(),
                    'totalLikes'=>$likesRepository->likesParPost($id),
                    'isLiked'=> $liked,
                    'comments' => $comments,
                    'id' => $id,
                ]
            );
        }
        return $this->render('frontoffice/blog_details.html.twig',[
                'post'=>$repository->findBy(['id'=>$id]) ,
                'category'=>$categoryRepository->findAll(),
                'totalLikes'=>$likesRepository->likesParPost($id),
                'isLiked'=>false,
                'comments' => $comments,
                'id' => $id,
            ]
        );
        }
    #[Route('/comments/{id}', name: 'comments')]
    public function Comments(FlashyNotifier $flashy, CurlService $client, Request $request, $id, PostsRepository $repository, PostCategoryRepository $categoryRepository, CommentaireRepository $commentairerepository): Response
    {

        //print_r($comment1->getBlogId());


        /*  $form = $this->createFormBuilder()
              ->add('Message', TextareaType::class, [
                  'attr' => ['placeholder' => 'your comments'],
                  'label' => false,
              ])
              ->add('name')
              ->add('email')->getForm()
              //   ->add('Submit', SubmitType::class)
              //   ->add("captcha", HiddenType::class, [
              //       "constraints" => [
              //           new NotNull(),
              //           new NotBlank()
              //       ]
              //   ])


          ;

          $form->handleRequest($request);*/
        //     $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$form->get('captcha')->getData()}";

        //$response = $client->curlManager($url);

        /*   if ($form->isSubmitted() && $form->isValid()) {
               dd(($request->request->get("ajax")));

               //  if (empty($response) || is_null($response)) {

               //      $flashy->warning("Something wrong!", '');
               //      return $this->redirectToRoute('contact');
               //  } else {
               //       $data = json_decode($response);
               //       if ($data->success) {
               $contenu = $form["Message"]->getData();
               $name = $form["name"]->getData();
               $email = $form["email"]->getData();
               $comment1->setMessage($contenu);
               $comment1->setName($name);
               $comment1->setEmail($email);
               $entityManager = $this->getDoctrine()->getManager();
               $entityManager->persist($comment1);
               $entityManager->flush();
               //   } else {
               //       $flashy->error("Confirm you are not robot!", '');
               //       return $this->redirectToRoute('app_contact');
               //   }
               //  }
               //       dd("hello");



           }*/
        // to display comments related to blog
        $comments = $commentairerepository->findByBlog($id);
        // partie creation comment
        $comment1 = new Commentaire();
        $article = $repository->findBy(['id' => $id]);
        $user = $this->getUser();
        $comment1->setUserid($user);
        $comment1->setBlogId($article[0]);

        if ($request->query->get('ajax')) {
            //     dd("hh");
            $comments = $commentairerepository->findByBlog($id);
            return new JsonResponse([
                "content" => $this->renderView('frontoffice/CommentList.html.twig', [
                    'comments' => $comments,
                ])

            ]);
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
