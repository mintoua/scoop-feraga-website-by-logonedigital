<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Services\MailerHelper;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\AppCustomAuthAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/s-inscrire', name: 'app_inscire')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        UserAuthenticatorInterface $userAuthenticator, 
        AppCustomAuthAuthenticator $authenticator, 
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerHelper $mail
        ): Response
    {
        $user = new User();
        $form =$this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            //dd("hello world");
            $user->setRoles(["ROLE_USER"]);

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );
            //dd($signatureComponents->getSignedUrl());
            $mail->send (
                        "MAIL DE VERIFICATION", 
                        $user->getEmail(),
                        "email/verificationmail.html.twig", 
                        ["verificationUrl" => $signatureComponents->getSignedUrl()],
                        "ngueemmanuel@gmail.com"
                    );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('frontoffice/s-inscire.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/verify", name="app_verify_email")
     */
    #[Route(path:'/verify', name:'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        VerifyEmailHelperInterface $verifyEmailHelper
        ):Response
    {
         $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }

        // Do not get the User's Id or Email Address from the Request object
        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_inscire');
        }

        // Mark your user as verified. e.g. switch a User::verified property to true
        $user->setIsVirified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Votre email a bien été confirmez.');

        return $this->redirectToRoute('app_login');
    }
}
