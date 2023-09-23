<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class IngredientTest extends WebTestCase
{
    public function testIsCreateIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        // Get url generator
        $urlGenerator = $client->getContainer()->get('router');
        // Get entityManager
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // User
        $user = $em->find(User::class, 1);
        $client->loginUser($user);

        // Form
        $crawler = $client->request('GET', $urlGenerator->generate('ingredient_new'));
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => uniqid('Ingredient_test_'),
            'ingredient[price]' => 25.6
        ]);
        $client->submit($form);

        // Redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('ingredient_index');
        $this->assertSelectorTextContains('div.alert-success', "L'ingrédient a été ajouté avec succès !");
    }

    public function testIfListIngredientSuccessfull(): void
    {
        $client = static::createClient();
        // Get url generator
        $urlGenerator = $client->getContainer()->get('router');

        // Get entityManager
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // User
        $user = $em->find(User::class, 1);
        $client->loginUser($user);

        // Route ingredient_index
        $crawler = $client->request('GET', $urlGenerator->generate('ingredient_index'));
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('ingredient_index');
    }

    public function testIsEditIngredientIsSuccessfull(): void
    {
        $client = static::createClient();
        // Get url generator
        $urlGenerator = $client->getContainer()->get('router');
        // Get entityManager
        /**
         * @var EntityManagerInterface $em
         */
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // User
        $user = $em->find(User::class, 1);
        $client->loginUser($user);

        // Ingredient
        $ingredient = $em->getRepository(Ingredient::class)->findOneBy(['user' => $user]);

        // Route
        $crawler = $client->request('GET', $urlGenerator->generate('ingredient_edit', ['id' => $ingredient->getId()]));
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => 'Ingredient édit',
            'ingredient[price]' => 50.65
        ]);
        $client->submit($form);

        // Redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('ingredient_index');
        $this->assertSelectorTextContains('div.alert-success', "L'ingrédient a été édité avec succès !");
    }

    public function testIfDeleteIngredientIsSuccefull(): void{
        $client = static::createClient();
        // Get url generator
        $urlGenerator = $client->getContainer()->get('router');
        // Get entityManager
        /**
         * @var EntityManagerInterface $em
         */
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        // User
        $user = $em->find(User::class, 1);
        $client->loginUser($user);

        // Ingredient
        $ingredient = $em->getRepository(Ingredient::class)->findOneBy(['user' => $user]);

        $crawler = $client->request('GET', $urlGenerator->generate('ingredient_delete', ['id' => $ingredient->getId()]));
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        $this->assertRouteSame('ingredient_index');
        $this->assertSelectorTextContains('div.alert-success', "L'ingrédient a été supprimé avec succès !");


    }
}