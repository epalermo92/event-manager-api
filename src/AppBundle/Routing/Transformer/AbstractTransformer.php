<?php declare(strict_types=1);

namespace AppBundle\Routing\Transformer;

use AppBundle\Exceptions\FormNotSubmittedException;
use AppBundle\Exceptions\FormNotValidException;
use AppBundle\RequestConverter\JsonStringConverter;
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
        JsonStringConverter::convertJsonStringToArray($request);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return left(new FormNotSubmittedException());
        }

        foreach ($form->getErrors() as $error) {
            var_dump($error->getMessage());
        }

        if (!$form->isValid()) {
            return left(new FormNotValidException());
        }

        return $this->doTransform($form);
    }

    public function transformLazy(): callable
    {
        return function (array $in): Either {
            return $this->transform(...$in);
        };
    }
}
