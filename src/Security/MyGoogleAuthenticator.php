<?php

namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class MyGoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $encoder;
    private $authChecker;
    private UrlGeneratorInterface $urlGenerator;
    

    public function __construct(
    ClientRegistry $clientRegistry, 
    EntityManagerInterface $entityManager, 
    RouterInterface $router,
    UserPasswordHasherInterface $encoder,
    AuthorizationCheckerInterface $authChecker,
    UrlGeneratorInterface $urlGenerator,
    private SessionInterface $session,
    private FlasherInterface $flasher
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->encoder = $encoder;
        $this->authChecker = $authChecker;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google_main');

        //dd($client);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                

                $email = $googleUser->getEmail();


                // 1) have they logged in with Facebook before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);

                if ($existingUser) {
                    return $existingUser;
                }

                // 2) do we have a matching user by email?
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                // 3) Maybe you just want to "register" them by creating
                // a User object
                if(!$user){
                    $user = new User(); 
                    //dd($googleUser);
                    
                    $user->setEmail($googleUser->getEmail());
                    $user->setGoogleId($googleUser->getId());
                    $user->setFirstname($googleUser->getFirstname());
                    $user->setLastname($googleUser->getLastname()); 
                    $user->setIsVirified(true);
                    $user->setBlocked(false);
                    //$user->
                    $user->setRgpd(true);
                    $user->setRoles(['ROLE_USER']);
                    $hashedPassword =$this->encoder->hashPassword($user,md5(uniqid()));

                    $user->setPassword($hashedPassword);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }   

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        
        if($this->authChecker->isGranted('ROLE_ADMIN') ){
            return new RedirectResponse($this->urlGenerator->generate('admin'));
          }else if($this->authChecker->isGranted('ROLE_USER')){
      
            $redirectUrl = $this->session->get('redirect_url');
            // dd($redirectUrl);
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
           //dd('hello world!');
            $path = parse_url($redirectUrl, PHP_URL_PATH);
            if(parse_url($redirectUrl, PHP_URL_PATH) === $cartUrl){
                $this->flasher->addFlash('Succès !');
                return new RedirectResponse($redirectUrl);
            }else if($redirectUrl === $blogDetailUrl){
                $this->flasher->addFlash('Succès !');
                return new RedirectResponse($redirectUrl);
            }
            $this->flasher->addFlash('Succès !');
            return new RedirectResponse($this->urlGenerator->generate('app_user_account'));
          }

        // $targetUrl = $this->router->generate('app_home');
          $this->flasher->addFlash('Succès !');
        return new RedirectResponse($this->urlGenerator->generate('app_user_account'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}