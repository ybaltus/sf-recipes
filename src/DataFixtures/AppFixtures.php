<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
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

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for($i=0; $i < 50; $i++){
            $product = $this->newIngredient($this->ingredientNames[$i]);
            $manager->persist($product);
        }
        $manager->flush();
    }

    private function newIngredient(string $name): Ingredient{
        return (new Ingredient())
            ->setName($name)
            ->setPrice(rand(1, 200))
            ;
    }
}
