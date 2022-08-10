<?php

namespace App\Security;

use Flasher\Prime\FlasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class AppCustomAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $authChecker;
    private $container;
    private $session;

    public function __construct(
    private UrlGeneratorInterface $urlGenerator, 
    private RouterInterface $router,
    AuthorizationCheckerInterface $authChecker,
    ParameterBagInterface $container,
    SessionInterface $session,
    private FlasherInterface $flasher
    )
    {
        $this->authChecker = $authChecker;
        $this->container = $container;
        $this->session = $session;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {   
          if($this->authChecker->isGranted('ROLE_ADMIN') ){
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
            //dd($redirectUrl.' </br>'.$blogDetailUrl);
            if(parse_url($redirectUrl, PHP_URL_PATH) === $cartUrl){
                $this->flasher->addSuccess('Succès !');
                return new RedirectResponse($redirectUrl);
            }else if($redirectUrl === $blogDetailUrl){
                $this->flasher->addSuccess('Succès !');
                return new RedirectResponse($redirectUrl);
            }
            $this->flasher->addSuccess('Succès !');
            return new RedirectResponse($this->urlGenerator->generate('app_user_account'));
          }
          $this->flasher->addSuccess('Succès !');
          return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
