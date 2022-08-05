<?php

namespace App\Controller;

use Flasher\Prime\FlasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack, private FlasherInterface $flasher)
    {
         $this->requestStack = $requestStack;
    }

    #[Route(path: '/se-connecter', name: 'app_login')]
   
    public function login(AuthenticationUtils $authenticationUtils, Request $request, SessionInterface $session): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user_account');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error){
            $this->flasher->addError("Votre mot de passe et\ou votre adresse email est incorrecte.");
            return $this->redirectToRoute('app_login');
        }
        // last username entered by =the user
        $lastUsername = $authenticationUtils->getLastUsername();
        //$container = $this->getContainer();
        
        $url = $request->headers->get('referer');
        
        if($session->get('redirect_url')){
            $session->remove('redirect_url');
        }
        $session->set('redirect_url', $url);
        
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
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
