<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\RequestConverter\JsonStringConverter;
use AppBundle\Routing\FormType\EventFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\EventTransformer;
use AppBundle\Service\EntityPersister;
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

class EventsController extends Controller
{
    /** @var EntityPersister */
    private $entityPersister;

    public function __construct(EntityPersister $entityPersister)
    {
        $this->entityPersister = $entityPersister;
    }

    /**
     * @Route("/api/events",name="post-events",methods={"POST"})
     */
    public function postEventsAction(Request $request): JsonResponse
    {

        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            EventTransformer::create()->transformLazy(),
            bind($this->entityPersister->buildSave())
        )(
            [
                $this->createForm(
                    EventFormType::class,
                    null,
                    ['method' => Request::METHOD_POST]
                ),
                $request,
            ]
        );

        return $r->either(
            ResponseLeftHandler::handle(),
            static function (Event $event) {
                return JsonResponse::create(
                    $event,
                    JsonResponse::HTTP_CREATED
                );
            }
        );
    }

    /**
     * @Route("/api/events",name="get-events",methods={"GET"})
     */
    public function getEventsAction(): JsonResponse
    {
        return JsonResponse::create(
            $this
                ->get('doctrine.orm.default_entity_manager')
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
        return ($this->entityPersister->buildDelete()($event))
            ->either(
                ResponseLeftHandler::handle(),
                static function () {
                    return JsonResponse::create();
                }
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
