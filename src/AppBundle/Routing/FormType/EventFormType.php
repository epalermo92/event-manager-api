<?php declare(strict_types=1);

namespace AppBundle\Routing\FormType;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\NaturalIdentity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractRequestType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'Nome',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'description',
            TextType::class,
            [
                'label' => 'descrizione',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'place',
            TextType::class,
            [
                'label' => 'Luogo',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'num_max_participants',
            NumberType::class,
            [
                'label' => 'Num max participanti',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'organizer',
            EntityType::class,
            [
                'class' => AbstractIdentity::class,
                'label' => 'organizer',
                'required' => true,
                'constraints' => [
                    new  NotBlank(),
                ],
            ]
        );

        $builder->add(
            'participants',
            EntityType::class,
            [
                'class' => NaturalIdentity::class,
                'label' => 'Num max participanti',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );
    }
}
