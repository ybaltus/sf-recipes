<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('', name: 'contact_index')]
    public function index(Request $request, EntityManagerInterface $em, MailService $mailService): Response
    {
        $contact = new Contact();
        $currentUser = $this->getUser();
        $optionsForm = $currentUser ? [
            'user_fullname' => $currentUser->getFullName(),
            'user_email' =>$currentUser->getEmail()
        ] : [];

        $form = $this->createForm(ContactType::class, $contact, $optionsForm);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isSubmitted()){
            $contact = $form->getData();
            $em->persist($contact);
            $em->flush();

            $mailService->sendEmail(
                $contact->getEmail(),
                'admin@sf6recipe.fr',
                $contact->getSubject() ?: 'Message sans sujet',
                'emails/contact.html.twig',
                ['contact' => $contact]
            );

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé.'
            );
            return $this->redirectToRoute('contact_index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
