<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Routing\FormType\EventFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\EventTransformer;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\pipeline;
use function Widmogrod\Monad\Either\left;
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
                function (Event $event): callable {
                    return $this->get('entity_persister')->buildSave($event);
                }
            )
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
            static function (callable $func) {
                return JsonResponse::create(
                    [
                        'event' => $func(),
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
        $events = $this
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository(Event::class)
            ->findAll();

        return JsonResponse::create($events);
    }

    /**
     * @Route("/api/events/{event}",name="put-events",methods={"PUT"})
     * @return JsonResponse
     */
    public function putEventsAction(Request $request, Event $event): JsonResponse
    {
        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            function (array $in): Either {
                return EventTransformer::create()->transform(...$in);
            },
            bind(
                function (Event $event): Either {
                    $this->get('entity_persister')->buildUpdate($event);

                    return right($event);
                }
            )
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
                    [
                        'id event updated' => $event->getId(),
                    ],
                    JsonResponse::HTTP_OK
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
        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            function (array $in): Either {
                if (!$in[0]) {
                    return left(new \AppBundle\Exceptions\EntityNotFoundException());
                }

                return right($in[0]);
            },
            bind(
                function (Event $event): Either {
                    $this->get('entity_persister')->buildDelete($event);

                    return right($event);
                }
            )
        )(
            [
                $this
                    ->get('doctrine.orm.default_entity_manager')
                    ->getRepository(Event::class)
                    ->find($event),
            ]
        );

        return $r->either(
            ResponseLeftHandler::handle(),
            function (Event $event) {
                return JsonResponse::create(
                    [
                        'event delete' => $event,
                    ]
                );
            }
        );
    }

    /**
     * @Route("/api/events/{event}",name="get-event",methods={"GET"})
     * @return JsonResponse
     */
    public function getEventAction(Event $event): JsonResponse
    {
        /** @var Either<\Exception, Either> $r */
        $r = pipeline(
            function (array $in): Either {
                if (!$in[0]) {
                    return left(new \AppBundle\Exceptions\EntityNotFoundException());
                }

                return right($in[0]);
            },
            bind(
                function (Event $event): Either {
                    return right($event);
                }
            )
        )(
            [
                $this
                    ->get('doctrine.orm.default_entity_manager')
                    ->getRepository(Event::class)
                    ->find($event),
            ]
        );

        return $r->either(
            ResponseLeftHandler::handle(),
            function (Event $event) {
                return JsonResponse::create(
                    [
                        'event' => $event,
                    ]
                );
            }
        );
    }
}
