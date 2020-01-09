<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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

        $form->get('name')->setData('Christmas Party');


        return JsonResponse::create(
            ['data' => $form->getData()]
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
}
