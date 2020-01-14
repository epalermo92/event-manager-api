<?php declare(strict_types=1);

namespace AppBundle\Routing\Transformer;

use AppBundle\Exceptions\FormNotValidException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Monad\Either\left;

abstract class AbstractTransformer
{
    abstract protected function doTransform(FormInterface $form): Either;

    protected function __construct()
    {
    }

    public static function create(): AbstractTransformer
    {
        return new static();
    }

    public function transform(FormInterface $form, Request $request): Either
    {
        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid()) {
            return left(new FormNotValidException());
        }

        return $this->doTransform($form);
    }
}
