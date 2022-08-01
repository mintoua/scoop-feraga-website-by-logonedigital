<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class SecurityController extends AbstractController
{
    #[Route(path: '/se-connecter', name: 'app_login')]
   
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        //$bag = new HeaderBag(array('cache-control' => 'no-cahe, no-store, private, max-age=0, must-relalidate'));
        
        //dd($request->headers('Cache-Control', 'no-cache, no-store, max-age=, must-revalidate'));
        $response = $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
        // $response->setPublic(false);
        // $response->setMaxAge(0);
        // $response->headers->addCacheControlDirective('must-revalidate', true);
        // $response->headers->addCacheControlDirective('no-cache', true);
        // $response->headers->addCacheControlDirective('no-store', true);
        $response->setCache([
            'must_revalidate'  => true,
            'no_cache'         => true,
            'no_store'         => true,
            'public'           => false,
            'private'          => true,
            'max_age'          => 0,
        ]);
        return $response;
    }

    #[Route(path: '/me-deconnecter', name: 'app_logout')]
  
    public function logout(Request $request): void
    {
        $response = new Response();
        $response->setCache([
            'must_revalidate'  => true,
            'no_cache'         => true,
            'no_store'         => true,
            'public'           => false,
            'private'          => true,
            'max_age'          => 0,
        ]);
    }

    #[Route(path: '/connect/facebook', name: 'app_facebook_connect')]
    public function connect(ClientRegistry $clientRegistry){
       
        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'public_profile', 'email' // the scopes you want to access
            ]);
    }

    #[Route(path: '/connect/google', name: 'app_google_connect')]
    public function googleConnect(ClientRegistry $clientRegistry){
        //dd($clientRegistry);
        return $clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }
    
    
}
