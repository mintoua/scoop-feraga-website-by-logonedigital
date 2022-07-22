<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    #[Route('/contacts', name: 'app_contact')]
    public function contact(Request $req, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        
        $form->handleRequest($req);
        
        if($form->isSubmitted() and $form->isValid()){
            
            $em->persist($contact);
            $em->flush();

            $mail = new Mail();
            $mail->send($contact->getEmail(), $contact->getPrenom(), 'NOUVEAU CONTACT', $contact->getMsg());
            

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('frontoffice/contacts.html.twig', [
            'form'=>$form->createView()
        ]);
    }
}
