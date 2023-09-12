<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recipe')]
class RecipeController extends AbstractController
{
    #[Route('', name: 'recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
//        $recipes = $recipeRepository->findBy(['isLocked' => false], ['name' => 'ASC']);
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user' => $this->getUser(),'isLocked' => false], ['name'=>'ASC']), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/new', name: 'recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $em->persist($recipe);
            $em->flush();

            $this->addFlash(
                'success',
                'La recette à été ajouté avec succès !'
            );

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, Recipe $recipe): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash(
                'success',
                'La recette à été éditer avec succès !'
            );

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'recipe_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $em, Recipe $recipe): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash(
            'success',
            'La recette à été supprimée avec succès !'
        );
        return $this->redirectToRoute('recipe_index');
    }
}
