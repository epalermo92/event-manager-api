<?php


namespace AppBundle\Routing\Transformer;

use AppBundle\Routing\FormType\EventFormType;
use Symfony\Component\Form\FormInterface;
use Widmogrod\Monad\Either\Either;

class EventTransformer implements TransformerInterface
{
    /** @var string $name */
    private $name;

    /** @var string $description */
    private $description;

    /** @var string $place */
    private $place;

    /** @var int $num_max_participants */
    private $num_max_participants;

    private function __construct(FormInterface $form)
    {
        $this->name = $form->get('name');
        $this->description = $form->get('description');
        $this->place = $form->get('place');
        $this->num_max_participants = $form->get('num_max_participants');
    }

    public static function create(FormInterface $form): EventTransformer
    {
        return new self($form);
    }

    /**
     * @inheritDoc
     */
    public function DoTransformValid()
    {
        // TODO: Implement DoTransformValid() method.
    }

    public function DoTransformInvalid(): Either
    {
        // TODO: Implement DoTransformInvalid() method.
    }
}
