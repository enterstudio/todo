<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenusControllerTest extends WebTestCase
{
    /**
     *
     *@covers GenusController::showAction
     *
     */
    public function testShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/genus/kevin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('AquaNote', $crawler->filter('h1')->text());
        $this->assertContains('Todo List!', $crawler->filter('title')->text());
    }
}
