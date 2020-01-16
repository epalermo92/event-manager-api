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
use function Widmogrod\Monad\Either\right;


class EventsController extends Controller
{
    /**
     * @Route("/api/events",name="post-events")
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
     * @Route("/api/events/{id}",name="put-events")
     * @return JsonResponse
     */
    public function putEventsAction(Request $request, $id): JsonResponse
    {
        /** @var Either<\Exception, Event> $r */
        $r = pipeline(
            function (array $in) use ($id): Either {
                /** @var FormInterface $form */
                $form = $in[0];
                /** @var Event $event */
                $event = $this->get('entity_persister')->getManager()->getRepository(Event::class)->find($id);
                $event->updateEntity(
                    $form->get('place')->getData(),
                    $form->get('name')->getData(),
                    $form->get('num_max_participants')->getData(),
                    $form->get('description')->getData()
                );

                return right($event);
            },
            bind(
                function (Event $event): Either {
                    $this->get('entity_persister')->getManager()->flush();

                    return right($event);
                }
            )
        )(
            [
                $this->createForm(
                    EventFormType::class,
                    [
                        'name' => 'Super Party!',
                        'description' => 'Festa di Natale',
                        'place' => 'Burigozzo 1',
                        'num_max_participants' => 300,
                        'organizer' => 1,
                        'participants' => 2,
                    ],
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
                        'id' => $event->getId(),
                    ],
                    JsonResponse::HTTP_OK
                );
            }
        );
    }

    /**
     * @Route("/api/events/{id}",name="delete-events")
     */
    public function deleteEventsAction($id)
    {
        /** @var Event $event */
        $event = $this->get('entity_persister')->getRepository(Event::class)->find($id);

        if (!$event){
            throw new EntityNotFoundException('Event not found');
        }

        $this->get('entity_persister')->delete($event);

        return JsonResponse::create(
            [
                'event deleted' => $event
            ]
        );
    }

    /**
     * @Route("/api/events/{id}",name="get-event",methods={"GET"})
     * @return JsonResponse
     */
    public function getEventAction($id): JsonResponse
    {
        /** @var Event $event */
        $event = $this->get('entity_persister')->getRepository(Event::class)->find($id);

        if (!$event){
            throw new EntityNotFoundException('Event not found!');
        }

        return JsonResponse::create(
            [
                'Event Name' => $event->getName()
            ]
        );
    }
}
