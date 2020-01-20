<?php declare(strict_types=1);

namespace Tests\AppBundle\Functional\Controller\Api;

use AppBundle\Entity\Event;
use AppBundle\Routing\FormType\EventFormType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testGetAllEvents(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/events');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testPostEvent(): void
    {
        $client = self::createClient();

        $entityManager = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $oldEventsNumber = count(
            $entityManager
                ->getRepository(Event::class)
                ->findAll()
        );

        $form = $client
            ->getContainer()
            ->get('form.factory')
            ->create(EventFormType::class);

        $client
            ->request(
                'POST',
                '/api/events',
                [
                    'name' => 'Christmas Party',
                    'place' => 'Burigozzo 1',
                    'description' => 'Festa di Natale',
                    'num_max_participants' => 300,
                ]
            );
        $form->handleRequest($client->getRequest());
        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        $newEventsNumber = count(
            $entityManager
                ->getRepository(Event::class)
                ->findAll()
        );

        $this->assertSame($oldEventsNumber + 1, $newEventsNumber);
        $this->assertStringContainsString('', $client->getResponse()->getContent());
    }

    public function testDeleteEvent(): void
    {
        $client = self::createClient();

        $entityManager = $client
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $oldEventsNumber = count(
            $entityManager
                ->getRepository(Event::class)
                ->findAll()
        );

        $client
            ->request(
                'DELETE',
                '/api/events/1'
            );

        $newEventsNumber = count(
            $entityManager
                ->getRepository(Event::class)
                ->findAll()
        );

        $this->assertSame($oldEventsNumber - 1, $newEventsNumber);
        $this->assertStringContainsString('', $client->getResponse()->getContent());
    }
//
//    public function testPutEvent(): void
//    {
//    }


//    public function testGetEvent(): void
//    {
//        $client = self::createClient();
//        $client->request('GET', '/api/events/1');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertJson($client->getResponse()->getContent());
//        $this->assertS
//    }
}
