<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\RequestConverter\JsonStringConverter;
use AppBundle\Routing\FormType\EventFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\EventTransformer;
use AppBundle\Service\EntityPersister;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\map;
use function Widmogrod\Functional\pipeline;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

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
                ->findAll()
        );
    }

    /**
     * @Route("/api/events/{event}",name="put-events",methods={"PUT"})
     * @return JsonResponse
     */
    public function putEventsAction(Request $request, Event $event): JsonResponse
    {

        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            EventTransformer::create()->transformLazy(),
            map([$event, 'updateEntity']),
            bind($this->entityPersister->buildUpdate())
        )(
            [
                $this->createForm(
                    EventFormType::class,
                    null,
                    ['method' => Request::METHOD_PUT]
                ),
                $request,
            ]
        );

        return $r->either(
            ResponseLeftHandler::handle(),
            static function (Event $event) {
                return JsonResponse::create(
                    $event,
                    JsonResponse::HTTP_ACCEPTED
                );
            }
        );
    }

    /**
     * @Route("/api/events/{event}",name="delete-events",methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteEventsAction(Event $event): JsonResponse
    {
        return self::handleEither(
            $this->entityPersister->buildDelete()($event)
        );
    }

    /**
     * @Route("/api/events/{event}",name="get-event",methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getEventAction(Event $event): JsonResponse
    {
        return JsonResponse::create(['event' => $event]);
    }
}
