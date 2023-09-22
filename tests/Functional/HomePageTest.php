<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();

        $buttonIncription = $crawler->filter('#btn_signin');
        $this->assertEquals(1, count($buttonIncription));

        $recipes = $crawler->filter('.recipes .card');
        $this->assertEquals(5, count($recipes));

        $this->assertSelectorTextContains('h1', 'Bienvenue sur Sf6Recette!');
    }
}
