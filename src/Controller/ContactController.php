<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('', name: 'contact_index')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
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

            $this->sendEmail($mailer, $contact);

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

    private function sendEmail(MailerInterface $mailer, Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('admin@sf6recipe.fr')
            ->subject($contact->getSubject() ?: 'Message sans sujet')
            // path of the Twig template to render
            ->htmlTemplate('emails/contact.html.twig')
            // pass variables (name => value) to the template
            ->context([
                'contact' => $contact
            ]);

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            var_dump($e->getMessage());
        }
    }
}
