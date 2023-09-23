<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findPublicRecipe(5);

        return $this->render('pages/home/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recipe/public', name: 'recipe_index_public', methods: ['GET'])]
    public function indexRecipePublic(RecipeRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $cache = new FilesystemAdapter();
        $dataCache = $cache->get('recipes_public', function (ItemInterface $item) use ($recipeRepository): array {
            $item->expiresAfter(10);
            return $recipeRepository->findPublicRecipe(null);
        });

        $recipes = $paginator->paginate(
            $dataCache,
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
        ]);
    }
}
