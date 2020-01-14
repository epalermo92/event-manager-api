<?php declare(strict_types=1);

namespace AppBundle\Routing\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractRequestType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'allow_extra_fields' => true,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
