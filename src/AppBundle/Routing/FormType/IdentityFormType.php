<?php declare(strict_types=1);

namespace AppBundle\Routing\FormType;

use AppBundle\Entity\AbstractIdentity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class IdentityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ]
        );

        $builder->add(
            'surname',
            TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['isNatural']])
                ]
            ]
        );

        $builder->add(
            'cf',
            TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['isNatural']])
                ]
            ]
        );

        $builder->add(
            'pi',
            TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['isLegal']])
                ]
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                'choices' => [
                    AbstractIdentity::LEGAL,
                    AbstractIdentity::NATURAL,
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'validation_groups' => static function (FormInterface $form) {
                    switch ($form->get('type')->getData()) {
                        case AbstractIdentity::LEGAL: return ['Default', 'isLegal'];
                        case AbstractIdentity::NATURAL: return ['Default', 'isNatural'];
                        default: throw new \RuntimeException('type not found');
                    }
                },
            ]
        );
    }
}
