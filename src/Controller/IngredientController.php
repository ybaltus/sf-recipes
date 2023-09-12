<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * List of ingredients
     * @param Request $request
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient_index', methods: ['GET'])]
    public function index(Request $request,IngredientRepository $ingredientRepository, PaginatorInterface $paginator): Response
    {
//        $ingredients = $ingredientRepository->findBy(['isLocked' => false], ['name'=>'ASC']);
        $ingredients = $paginator->paginate(
            $ingredientRepository->findBy(['user' => $this->getUser(),'isLocked' => false], ['name'=>'ASC']), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredient/new', name: 'ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response{
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());

            $em->persist($ingredient);
            $em->flush();

            $this->addFlash(
                'success',
                "L'ingrédient a été ajouté avec succès !"
            );

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/edit/{id}', name: 'ingredient_edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $em): Response{
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
//            $ingredient = $form->getData();
//            $em->persist($ingredient);
            $em->flush();

            $this->addFlash(
                'success',
                "L'ingrédient a été édité avec succès !"
            );

            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/delete/{id}', name: 'ingredient_delete', methods: ['GET', 'POST'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $em): Response{
        $em->remove($ingredient);
        $em->flush();

        $this->addFlash(
            'success',
            "L'ingrédient a été supprimé avec succès !"
        );

        return $this->redirectToRoute('ingredient_index');
    }
}
