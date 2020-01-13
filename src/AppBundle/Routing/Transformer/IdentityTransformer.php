<?php


namespace AppBundle\Routing\Transformer;


use AppBundle\Builder\IdentityBuilder;
use Symfony\Component\Form\FormInterface;

class IdentityTransformer
{
    public static function transform(FormInterface $form)
    {
        return IdentityBuilder::build($form->get('type')->getData(), $form->get('name')->getData(), $form->get('surname')->getData(), $form->get('codice')->getData());
    }
}
