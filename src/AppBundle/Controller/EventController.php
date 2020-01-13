<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Routing\Transformer\EventTransformer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Left;
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

        //EventTransformer::transform($form);
        //TODO
        $organizer = $this->getDoctrine()->getManager()->getRepository(AbstractIdentity::class)->find(2);
        $participant = $this->getDoctrine()->getManager()->getRepository(AbstractIdentity::class)->find(7);

//        $event = new Event($place,new DateTime(),$name,$num_max_participants,$description,$organizer,$participant);

        return JsonResponse::create(
            ['data' => $event]
        );
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
//        $em = $this->getDoctrine()->getManager();
//        $event = $em->getRepository(Event::class)->find($id);
//
//        if (!$event) {
//            return left(new NotFoundHttpException('No event found for id '.$id));
//        }
//
//        $event->setName('New product name!');
//        $em->flush();
//
//        return JsonResponse::create([
//            'result' => true
//        ]);
        //TODO
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
            return left(new NotFoundHttpException('No event found for id '.$id));
        }

        $em->remove($event);
        $em->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }
}
