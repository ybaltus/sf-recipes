<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\Ingredient;
use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    private array $ingredientNames = [
        'Farine',
        'Eau',
        'Oeuf',
        'Sucre',
        'Sel',
        'Poivre',
        'Huile olive',
        'Huile tournesol',
        'Banane',
        'Levure chimique',
        'Extrait de vanille',
        'Canelle',
        'Muscade',
        'Citron',
        'Pomme',
        'Abricot',
        'Ail',
        'Poulet',
        'Boeuf',
        'Veau',
        'Poisson',
        'Origan',
        'Basilic',
        'Crème fraîche',
        'Yaourt',
        'Menthe',
        'Amande',
        'Ananas',
        'Anis',
        'Arachide',
        'Pomme de terre',
        'Tomate',
        'Asperge',
        'Artichaut',
        'Aspartame',
        'Aubergine',
        'Courgette',
        'Avocat',
        'Olive',
        'Avoine',
        'Chapelure',
        'Miel',
        'Beurre',
        'Chocolat',
        'Rhum',
        'Vin',
        'Soja',
        'Chou',
        'Curcuma',
        'Basilic'
    ];

    private array $recipeNames = [
        'Salade César',
        'Oeufs mollets',
        'Falafel',
        'Soupe à l\'oignon',
        'tarte au poireaux',
        'Tarte au thon',
        'Artichaud nature',
        'Cake salé',
        'Soupe de poids cassés',
        'Asperges blanches',
        'Tacos mexicains',
        'Omelette nature',
        'Salade pâtes au thon',
        'Bruchetta',
        'Samoussa au boeuf',
        'Samoussa au poulet',
        'Samoussa végétarien',
        'Flan de courgette',
        'Gratin de pomme de terre',
        'Salade de riz',
        'Quiche lorraine',
        'Raviolis japonais',
        'Taboulé',
        'Couscous royale',
        'Salade composée',
        'Magret de canard',
        'Bagels',
        'Sandwich',
        'hamburger',
        'Bokit au poulet',
        'Bokit au jambon',
        'Pavé de saumon'
    ];

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Users
        $admin = (new User())
            ->setFullName('Admin SF recipe')
            ->setEmail('admin@sf6recipe.com')
            ->setPlainPassword('password')
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
        ;
        $manager->persist($admin);
        $users[]= $admin;
        for($i=0; $i < 10; $i++){
            $user = $this->newUser();
            $users[] = $user;
            $manager->persist($user);
        }



        // Ingredients
        $ingredients = [];
        for($i=0; $i < 50; $i++){
            $ingredient = $this->newIngredient($this->ingredientNames[$i], $users);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes
        $recipes = [];
        for($i=0; $i < 25; $i++){
            $recipe = $this->newRecipe($this->recipeNames[$i], $ingredients, $users);
            $recipes[] = $recipe;
            $manager->persist($recipe);
        }

        // Marks
        foreach($recipes as $recipe){
            for($i=0; $i < mt_rand(0, 4); $i++){
                $mark = $this->newMark($recipe, $users);
                $manager->persist($mark);
            }
        }

        // Contacts
        for ($i=0; $i <5; $i++){
            $contact = $this->newContact($i);
            $manager->persist($contact);
        }

        $manager->flush();
    }

    private function newIngredient(string $name, $users): Ingredient{
        return (new Ingredient())
            ->setName($name)
            ->setPrice(mt_rand(1, 200))
            ->setUser($users[mt_rand(0, count($users)-1)])
            ;
    }

    private function newRecipe(string $name, array $ingredients, $users): Recipe{
        $recipe =  (new Recipe())
            ->setName($name)
            ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
            ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
            ->setDescription($this->faker->text(300))
            ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
            ->setIsFavorite(mt_rand(0, 1) == 1)
            ->setUser($users[mt_rand(0, count($users)-1)])
            ->setIsPublic(mt_rand(0,1) == 1)
        ;
        for($i = 0; $i < mt_rand(5, 15); $i++) {
            $recipe->addIngredient($ingredients[mt_rand(0, 49)]);
        }
        return $recipe;
    }

    private function newUser(): User{
        return  (new User())
            ->setFullName($this->faker->name())
            ->setPseudo(mt_rand(0, 1) == 1 ? $this->faker->firstName() : null)
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('password')
            ;
    }

    private function newMark(Recipe $recipe, array $users): Mark{
        return (new Mark())
            ->setMark(mt_rand(1, 5))
            ->setRecipe($recipe)
            ->setUser($users[mt_rand(0, count($users)-1)])
            ;
    }

    private function newContact(int $indice): Contact{
        return  (new Contact())
            ->setFullName($this->faker->name())
            ->setEmail($this->faker->email())
            ->setSubject('Demande n°'.$indice)
            ->setMessage($this->faker->text(300))
            ;
    }
}
