<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/show/{id}', name: 'recipe_show', methods: ['GET', 'POST'])]
    #[IsGranted('recipe_view', 'recipe')]
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $em, MarkRepository $markRepository): Response{
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser());
            $mark->setRecipe($recipe);

            $existingMark = $markRepository->findOneBy(['user' => $this->getUser(), 'recipe' =>$recipe]);
            if(!$existingMark) {
                $em->persist($mark);
            }else {
              $existingMark->setMark($form->getData()->getMark());
            }

            $em->flush();

            $this->addFlash(
                'success',
                'Votre note à bien été enregistrée.'
            );

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('pages/recipe/show.html.twig',[
            'recipe' => $recipe,
            'form' => $form->createView()
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
    #[IsGranted('user_edit', 'recipe')]
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
    #[IsGranted('user_edit', 'recipe')]
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
