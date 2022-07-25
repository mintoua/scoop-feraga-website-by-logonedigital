<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Services\CurlService;
use App\Services\MailerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contacts', name: 'app_contact')]
    public function contact(
        Request $req, 
        EntityManagerInterface $em,
        CurlService $client,
        FlashyNotifier $flashy,
        MailerHelper $mail
        ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        
        $form->add("captcha", HiddenType::class, [
            "constraints"=>[
                new NotNull(),
                new NotBlank()
            ]
        ]);

        $form->handleRequest($req);

        //hello world
         
        if($form->isSubmitted() and $form->isValid()){
            
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$form->get('captcha')->getData()}";

            $response = $client->curlManager($url);

            if(empty($response) || is_null($response)){
               $flashy->warning("Something wrong!",'');
                return $this->redirectToRoute('contact');
            }else{
                $data = json_decode($response);
                if($data->success){
                    $em->persist($contact);
                    $em->flush();

                    //$mail = new Mail();
                    $mail->send (
                        "NOUVEAU CONTACT", 
                        $contact->getEmail(),
                        $contact->getMsg(), 
                        ["",""],
                        "ngueemmanuel@gmail.com"
                    );
                    //$mail->send($contact->getEmail(), $contact->getPrenom(), 'NOUVEAU CONTACT', $contact->getMsg());
                    
                }else{
                    $flashy->error("Confirm you are not robot!",'');
                    return $this->redirectToRoute('app_contact');
                }
            }
            

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('frontoffice/contacts.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
