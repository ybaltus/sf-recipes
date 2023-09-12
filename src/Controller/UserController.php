<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignUpType;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/edit/{id}', name: 'user_edit')]
    public function index(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Verify the user is authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Verify the current user
        $currentUser = $this->getUser();
        if($currentUser !== $user){
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            // Verify if the password is right
            if($passwordHasher->isPasswordValid($user, $user->getPlainPassword())){
                $this->addFlash(
                    'success',
                    'Votre compte à été édité avec succès !'
                );

                $em->flush();
                return $this->redirectToRoute('recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/editPassword/{id}', name: 'user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, User $user): Response{
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            // Verify if the password is right
            if($passwordHasher->isPasswordValid($user, $data['plainPassword'])){
                $this->addFlash(
                    'success',
                    'Votre mot de passe à été édité avec succès !'
                );

                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword($data['newPassword']);

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }
        return $this->render('pages/user/editPassword.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
