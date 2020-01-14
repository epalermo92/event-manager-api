<?php declare(strict_types=1);

namespace AppBundle\Routing\Transformer;

use AppBundle\Builder\EventBuilder;
use Symfony\Component\Form\FormInterface;
use Widmogrod\Monad\Either\Either;

class EventTransformer extends AbstractTransformer
{
    public function doTransform(FormInterface $form): Either
    {
        return EventBuilder::build($form->getData());
    }
}
