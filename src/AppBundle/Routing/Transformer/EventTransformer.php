<?php declare(strict_types=1);

namespace AppBundle\Routing\Transformer;

use AppBundle\Builder\EventBuilder;
use Symfony\Component\Form\FormInterface;
use Widmogrod\Monad\Either\Either;

class EventTransformer
{
    public static function transform(FormInterface $form): Either
    {
        $data = [
          'name' => $form->get('name'),
          'description' => $form->get('description'),
          'place' => $form->get('place'),
          'num_max_participants' => $form->get('num_max_participants')
        ];

        return EventBuilder::build($data);
    }
}
