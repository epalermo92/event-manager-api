<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/create-event",name="create-event")
     */
    public function createEvent()
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('place', TextType::class)
            ->add('num_max_participants', NumberType::class)
            ->getForm();

        return $this->render('default/create_event.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/identity_creation", name="identity-creation")
     */
    public function createIdentity()
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Natural' => 'natural',
                    'Legal' => 'legal',
                ],
                'placeholder' => 'Add type',
                'required' => true,
            ])
            ->getForm();

        return $this->render('default/create_identity.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
