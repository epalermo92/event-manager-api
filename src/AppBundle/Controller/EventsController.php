<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Routing\FormType\EventFormType;
use AppBundle\Routing\Transformer\EventTransformer;
use AppBundle\Service\EntityPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\map;
use function Widmogrod\Functional\pipeline;

class EventsController extends AbstractController
{
    private $entityPersister;

    private $entityManager;

    public function __construct(EntityPersister $entityPersister, EntityManagerInterface $entityManager)
    {
        $this->entityPersister = $entityPersister;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/events",name="post-events",methods={"POST"})
     */
    public function postEventsAction(Request $request): JsonResponse
    {
        return self::handleEither(
            pipeline(
                EventTransformer::create()->transformLazy(),
                bind($this->entityPersister->buildSave())
            )(
                $this->sendForm($request, EventFormType::class, Request::METHOD_POST)
            )
        );
    }

    /**
     * @Route("/api/events",name="get-events",methods={"GET"})
     */
    public function getEventsAction(): JsonResponse
    {
        return self::buildResponse(
            $this
                ->entityManager
                ->getRepository(Event::class)
                ->findAll(),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/api/events/{event}",name="put-events",methods={"PUT"})
     */
    public function putEventsAction(Request $request, Event $event): JsonResponse
    {
        return self::handleEither(
            pipeline(
                EventTransformer::create()->transformLazy(),
                map([$event, 'updateEntity']),
                bind($this->entityPersister->buildUpdate())
            )(
                $this->sendForm($request, EventFormType::class, Request::METHOD_PUT)
            )
        );
    }

    /**
     * @Route("/api/events/{event}",name="delete-events",methods={"DELETE"})
     */
    public function deleteEventsAction(Event $event): JsonResponse
    {
        return self::handleEither(
            $this->entityPersister->buildDelete()($event)
        );
    }

    /**
     * @Route("/api/events/{event}",name="get-event",methods={"GET"})
     */
    public function getEventAction(Event $event): JsonResponse
    {
        return self::buildResponse($event, Response::HTTP_OK);
    }
}
