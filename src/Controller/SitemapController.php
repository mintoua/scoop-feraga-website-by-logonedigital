<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ["_format"=>"xml"])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        // Add Statics Url of the Website
        $urls[] = ['loc' => $this->generateUrl('app_home'),'priority'=>0.8];
        $urls[] = ['loc' => $this->generateUrl('app_about')];
        $urls[] = ['loc' => $this->generateUrl('app_activities'),'priority'=>0.7];
        $urls[] = ['loc' => $this->generateUrl('app_shop'),'priority'=>1.0];
        $urls[] = ['loc' => $this->generateUrl('app_blog'),'priority'=>0.8];
        $urls[] = ['loc' => $this->generateUrl('app_user_account')];
        $urls[] = ['loc' => $this->generateUrl('app_contact')];
        $urls[] = ['loc' => $this->generateUrl('app_cart')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];

        foreach ($entityManager->getRepository (Posts::class)->findAll () as $post) {
            $images = [
                'loc' => '/uploads/images/'.$post->getPostImage(), // URL to image
                'title' => $post->getTitle()    // Optional, text describing the image
            ];

            $urls[] = [
                'loc' => $this->generateUrl('blog_details', [
                    'slug' => $post->getSlug(),
                ]),
                'lastmod' => $post->getCreatedAt()->format('Y-m-d'),
                'image' => $images
            ];
        }

        foreach ($entityManager->getRepository (Product::class)->findAll () as $product) {
            $images = [
                'loc' => '/uploads/images/'.$product->getProductImage(), // URL to image
                'title' => $product->getProductName()    // Optional, text describing the image
            ];

            $urls[] = [
                'loc' => $this->generateUrl('app_single_product', [
                    'slug' => $product->getSlug(),
                ]),
                'image' => $images
            ];
        }


        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls,
                'hostname' => $hostname]),
            200
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
