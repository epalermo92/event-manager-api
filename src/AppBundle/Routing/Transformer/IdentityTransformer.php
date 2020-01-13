<?php


namespace AppBundle\Routing\Transformer;


use AppBundle\Builder\IdentityBuilder;
use AppBundle\Entity\AbstractIdentity;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;
use Symfony\Component\Form\FormInterface;

class IdentityTransformer implements TransformerInterface
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $surname
     */
    private $surname;

    /**
     * @var string $codiceFiscale
     */
    private $codiceFiscale;

    /**
     * @var string
     */
    private $type;

    public function __construct(FormInterface $form)
    {

        $this->name = $form->get('name');
        $this->surname = $form->get('surname');
        $this->codiceFiscale = $form->get('codiceFiscale');
        $this->type = $form->get('type');
    }

    public static function create(FormInterface $form): IdentityTransformer
    {
        return new self($form);
    }

    /**
     * @return Either
     */
    public function DoTransformValid()
    {
        return IdentityBuilder::build($this->type, $this->name, $this->surname, $this->codiceFiscale);
    }

    public function DoTransformInvalid(): Either
    {
        return new Left(new  \RuntimeException('The form is not valid.'));
    }

    public static function isValid(FormInterface $form): bool
    {
        return (is_string($form->get('name')) && is_string($form->get('surname')) && is_string($form->get('codiceFiscale')));
    }
}
