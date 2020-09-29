<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPost()
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/requete/2');

        self::assertSame(1, $crawler->filter('html:contains("GET")')->count());
    }

    public function loginFormTest()
    {

        /*$client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $crawler = $client->submitForm('Créer le compte', [
            'username' => 'zozor',
            'password' => 'Zoz0rIsHome',
        ]);*/
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful($pageName, $url)
    {
        $client = self::createClient();
        $client->catchExceptions(false);
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertTrue(
            $response->isSuccessful(),
            sprintf(
                'La page "%s" devrait être accessible, mais le code HTTP est "%s".',
                $pageName,
                $response->getStatusCode()
            )
        );
    }

    public function provideUrls()
    {
        return [
            'home' => ['home', '/']
            // autre page accessible
        ];
    }
}