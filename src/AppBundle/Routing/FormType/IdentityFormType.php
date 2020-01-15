<?php


namespace AppBundle\Routing\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IdentityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'name',
                'required' => true,
            ]
        );

        $builder->add(
            'surname',
            TextType::class,
            [
                'label' => 'surname',
                'required' => true,
            ]
        );

        $builder->add(
            'codice',
            TextType::class,
            [
                'label' => 'codice',
                'required' => true,
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'Persona',
                'choices' => [
                    'Natural' => 'natural',
                    'Legal' => 'legal',
                ],
                'placeholder' => 'Add type',
                'required' => true,
            ]
        );
    }
}
