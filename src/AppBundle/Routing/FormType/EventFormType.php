<?php declare(strict_types=1);

namespace AppBundle\Routing\FormType;

use AppBundle\Entity\AbstractIdentity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractRequestType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            IntegerType::class,
            [
                'label' => 'Num max partecipanti',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => 1]),
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
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'participants',
            CollectionType::class,
            [
                'entry_type' => IntegerType::class,
                'allow_add' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );
    }
}
