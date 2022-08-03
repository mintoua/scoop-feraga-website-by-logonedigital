<?php
namespace App\EventSubcriber;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorSucriber implements EventSubscriberInterface
{

    public function __construct(
    private UrlGeneratorInterface $urlGenerator, 
    private RouterInterface $router,
    private AuthorizationCheckerInterface $authChecker, 
    private SessionInterface $session
    )
    {
        
    }

    public static function getSubscribedEvents()
    {
        return [
            TwoFactorAuthenticationEvents::COMPLETE=>['twoFactorAuthenticationSuccess',3000]
        ];
    }

    /**
     * elle doit normalement redirect l'utilisateur après que l'authentification 2FA soit réussit
     *
     * @return Response
     */
    public function twoFactorAuthenticationSuccess():Response{
        //dd($this->authChecker->isGranted('ROLE_ADMIN'));
        
        if($this->authChecker->isGranted('ROLE_ADMIN') ){
           // dd(new RedirectResponse($this->urlGenerator->generate('admin')));
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        }else if($this->authChecker->isGranted('ROLE_USER')){
            $redirectUrl = $this->session->get('redirect_url');
            $cartUrl = $this->urlGenerator->generate('app_cart');
            $blogDetailUrl=null;

            //extraction du $slug dans la route
           try {
            $parts = parse_url($redirectUrl);
            $path_parts= explode('/', $parts['path']);
            $slug = $path_parts[2];
            $blogDetailUrl = $this->router->generate('blog_details', ['slug'=>$slug], urlGeneratorInterface::ABSOLUTE_URL);
           } catch (\Throwable $th) {
           
           }

           $path = parse_url($redirectUrl, PHP_URL_PATH);
            if(parse_url($redirectUrl, PHP_URL_PATH) === $cartUrl){
                return new RedirectResponse($redirectUrl);
            }else if($redirectUrl === $blogDetailUrl){
                return new RedirectResponse($redirectUrl);
            }
            return new RedirectResponse($this->urlGenerator->generate('app_user_account'));
        }
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        
    }
}