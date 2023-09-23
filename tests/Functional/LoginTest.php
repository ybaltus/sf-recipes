<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginTest extends WebTestCase
{
    public function testIsLoginIsSuccessfull(): void
    {
        $client = static::createClient();

        // get route by urlgenerator
        /**
         * @var UrlGeneratorInterface $urlGenerator
         */
        $urlGenerator = $client->getContainer()->get('router');
        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        // Handling form
        $submitButton = $crawler->selectButton('Se connecter');
        $form = $submitButton->form([
            '_username' => "admin@sf6recipe.com",
            '_password' => 'password'
        ]);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        // Check the login
        $this->assertRouteSame('home_index');

    }

    public function testIfLoginFailedWhenPasswordIsWrong():void{
        $client = static::createClient();

        // get route by urlgenerator
        /**
         * @var UrlGeneratorInterface $urlGenerator
         */
        $urlGenerator = $client->getContainer()->get('router');
        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        // Handling form
        $submitButton = $crawler->selectButton('Se connecter');
        $form = $submitButton->form([
            '_username' => "admin@sf6recipe.com",
            '_password' => 'passwdord'
        ]);
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();

        // Check the login
        $this->assertRouteSame('security_login');
        $this->assertSelectorTextContains('div.alert-primary', 'Invalid credentials.');

    }
}
