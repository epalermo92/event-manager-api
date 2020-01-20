<?php declare(strict_types=1);

namespace Tests\AppBundle\Functional\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testGetAllEvents():void
    {
        $client = self::createClient();
        $crawler = $client->request('GET','/api/events');
        
        var_dump($crawler->text());
    }
}
