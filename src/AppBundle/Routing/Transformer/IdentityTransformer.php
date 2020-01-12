<?php


namespace AppBundle\Routing\Transformer;


use AppBundle\Builder\IdentityBuilder;
use AppBundle\Entity\AbstractIdentity;
use AppBundle\Routing\FormType\IdentityFormType;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;

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

    public function __construct(IdentityFormType $form)
    {
        $this->name = $form['name'];
        $this->surname = $form['surname'];
        $this->codiceFiscale = $form['codiceFiscale'];
    }

    public static function create(IdentityFormType $form): IdentityTransformer
    {
        return new self($form);
    }

    /**
     * @return Either|AbstractIdentity
     */
    public function DoTransformValid()
    {
        return IdentityBuilder::build($this->name, $this->surname, $this->codiceFiscale);
    }

    public function DoTransformInvalid(): Either
    {
        return new Left(new  \RuntimeException('The form is not valid.'));
    }

    public static function isValid(IdentityFormType $form): bool
    {
        return (is_string($form['name']) || is_string($form['surname']) || is_string($form['type']));
    }
}
