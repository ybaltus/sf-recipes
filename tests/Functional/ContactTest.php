<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        // Get the form
        $submitButton = $crawler->selectButton('Envoyer');
        $form = $submitButton->form();

        $form['contact[fullName]'] = "Luffy Mugiwara";
        $form['contact[email]'] = "luffy@mugiwara.fr";
        $form['contact[subject]'] = "Sujet de test";
        $form['contact[message]'] = "Je suis le message de test !!! ";

        // Submit the form
        $client->submit($form);

        // Check http response
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Check the sending email
        $this->assertEmailCount(1);
        $client->followRedirect();

        // Check the success email
        $this->assertSelectorTextContains(
            'div.alert.alert-dismissible.alert-success',
            'Votre message a bien été envoyé.'
        );
    }
}
