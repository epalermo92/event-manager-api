<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Routing\FormType\EventFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\EventTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\pipeline;
use function Widmogrod\Monad\Either\right;


class EventsController extends Controller
{
    /**
     * @Route("/api/events",name="post-events",methods={"POST"})
     */
    public function postEventsAction(Request $request): JsonResponse
    {
        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            static function (array $in): Either {
                return EventTransformer::create()->transform(...$in);
            },
            bind(
                function (Event $event): Either {
                    $this->get('entity_persister')->save($event);
                    return right($event);
                }
            )
        )(
            [
                $this->createForm(
                    EventFormType::class,
                    [
                        'name' => 'Christmas Party',
                        'description' => 'Festa di Natale',
                        'place' => 'Burigozzo 1',
                        'num_max_participants' => 300,
                        'organizer' => 1,
                        'participants' => 2,
                    ],
                    ['method' => Request::METHOD_POST]
                ),
                $request,
            ]
        );

        return $r->either(
            ResponseLeftHandler::handle(),
            static function (Event $event) {
                return JsonResponse::create(
                    [
                        'id' => $event->getId(),
                    ],
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
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->findAll();

        return JsonResponse::create($events);
    }

    /**
     * @Route("/api/events/{event}",name="put-events",methods={"PUT"})
     * @return JsonResponse
     */
    public function putEventsAction($id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Event $event */
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create(
                [
                    'result' => false,
                ]
            );
        }

        $event->updateEntity(
            $event->getPlace(),
            $event->getName(),
            $event->getNumMaxParticipants(),
            $event->getDescription()
        );
        $em->flush();

        return JsonResponse::create(
            [
                'result' => true,
            ]
        );
    }

    /**
     * @Route("/api/events/{event}",name="delete-events",methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteEventsAction(Event $event): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create(
                [
                    'result' => false,
                ]
            );
        }

        $em->remove($event);
        $em->flush();

        return JsonResponse::create(
            [
                'result' => true,
            ]
        );
    }

    /**
     * @Route("/api/events/{event}",name="get-event",methods={"GET"})
     * @return JsonResponse
     */
    public function getEventAction($id): JsonResponse
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create(
                [
                    'result' => false,
                ]
            );
        }

        return JsonResponse::create(
            [
                'result' => true,
            ]
        );
    }
}
