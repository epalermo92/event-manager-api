<?php


namespace AppBundle\Routing\Transformer;


use AppBundle\Builder\IdentityBuilder;
use Symfony\Component\Form\FormInterface;
use Widmogrod\Monad\Either\Either;

class IdentityTransformer extends AbstractTransformer
{
    public function doTransform(FormInterface $form): Either
    {
        return IdentityBuilder::build($form->get('type')->getData(), $form->get('name')->getData(), $form->get('surname')->getData(), $form->get('codice')->getData());
    }
}
