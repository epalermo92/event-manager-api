<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Routing\Transformer\EventTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Right;

class EventsController extends Controller
{
    /**
     * @Route("/api/events/post",name="post-events",methods={"POST"})
     */
    public function postEventsAction(): JsonResponse
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('place', TextType::class)
            ->add('num_max_participants', NumberType::class)
            ->getForm();
        
        $form->get('name')->setData('Christams Party');
        $form->get('description')->setData('Festa di Natale');
        $form->get('place')->setData('Gadames');
        $form->get('num_max_participants')->setData('300');

        $event = EventTransformer::transform($form);

        $em = $this->getDoctrine()->getManager();

        if ($event instanceof Right) {
            $em->persist($event->extract());
            $em->flush();

            return JsonResponse::create([
                'result' => true
            ]);
        }

        return JsonResponse::create([
            'result' => false
        ]);
    }

    /**
     * @Route("/api/events/get",name="get-events",methods={"GET"})
     */
    public function getEventsAction(): JsonResponse
    {
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->findAll();

        return JsonResponse::create($events);
    }

    /**
     * @Route("/api/events/put/{id}",name="put-events",methods={"PUT"})
     * @return JsonResponse
     */
    public function putEventsAction($id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Event $event */
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create([
                'result' => false
            ]);
        }

        $event->updateEntity($event->getPlace(), $event->getName(), $event->getNumMaxParticipants(), $event->getDescription());
        $em->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/api/events/delete/{id}",name="delete-events",methods={"DELETE"})
     * @return JsonResponse
     */
    public function deleteEventsAction($id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create([
                'result' => false
            ]);
        }

        $em->remove($event);
        $em->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/api/events/get/{id}",name="get-event",methods={"GET"})
     * @return JsonResponse
     */
    public function getEventAction($id): JsonResponse
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($id);

        if (!$event) {
            return JsonResponse::create([
                'result' => false
            ]);
        }

        return JsonResponse::create([
           'result' => true
        ]);
    }
}
