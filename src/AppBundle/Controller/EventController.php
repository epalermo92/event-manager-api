<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Routing\Transformer\EventTransformer;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use function Widmogrod\Monad\Either\left;

class EventController extends Controller
{
    /**
     * @Route("/create-event",name="create-event")
     */
    public function create()
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

        if ($event instanceof Right)
        {
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
     * @Route("/list-events",name="list-events")
     */
    public function list()
    {
        $eventRepository = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepository->findAll();

        return JsonResponse::create($events);
    }

    /**
     * @Route("/update-event/{id}",name="update-event")
     * @return JsonResponse|Left
     */
    public function update($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Event $event */
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return left(new NotFoundHttpException('No event found for id '.$id));
        }

//        /** @var ArrayCollection $participants */
//        $participants = $event->getParticipants();
//        $event->updateEntity($event->getPlace(),$event->getDate(),$event->getName(),$event->getNumMaxParticipants(),$event->getDescription(),$event->getOrganizer(),$participants);
        $em->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/delete-event/{id}",name="delete-event")
     * @return JsonResponse|Left
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($id);

        if (!$event) {
            return left(new NotFoundHttpException('No event found for id ' . $id));
        }

        $em->remove($event);
        $em->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }
}
