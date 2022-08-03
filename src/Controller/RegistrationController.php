<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Services\MailerHelper;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
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
    /**
     * permet à un utilisateur de s'incrire dans la base de donnée
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param MailerHelper $mail
     * @return Response
     */

    #[Route('/s-inscrire', name: 'app_inscire')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerHelper $mail,
        FlashyNotifier $flashy
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
            $user->setBlocked(true);

            $entityManager->persist($user);
            $entityManager->flush();

            //permet de démarrer la procédure de vérifiction d'email
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $mail->send (
                        "MAIL DE VERIFICATION", 
                        $user->getEmail(),
                        "email/verificationmail.html.twig", 
                        ["verificationUrl" => $signatureComponents->getSignedUrl()],
                        "ngueemmanuel@gmail.com"
            );
            $this->addFlash('success', 'un email de confirmation vous a-été envoyé');
            return $this->redirectToRoute('app_login');
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
