<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    public function getEntity(string $name, string $description): Recipe
    {
        return  (new Recipe())
            ->setName($name)
            ->setDescription($description)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ;

    }

    public function testEntityIsValid(): void
    {
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) Test Recipe entity
        $recipe = $this->getEntity("Recipe #1", "Description #1");
        $errors = $container->get('validator')->validate($recipe);

        $this->assertCount(0, $errors);

    }

    public function testInvalidName(): void{
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) Test Recipe entity
        $recipe = $this->getEntity("", "Description #1");

        $errors = $container->get('validator')->validate($recipe);
        $this->assertCount(2, $errors);
    }

    public function testGetAverage(): void{
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();
        $user = $container->get('doctrine.orm.entity_manager')->find(User::class, 1);

        $recipe = $this->getEntity("Recipe #1", "Description #1");
        for($i=0; $i <5; $i++){
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe)
            ;

            $recipe->addMark($mark);
        }

        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
