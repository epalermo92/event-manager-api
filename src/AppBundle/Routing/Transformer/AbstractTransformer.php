<?php declare(strict_types=1);

namespace AppBundle\Routing\Transformer;

use AppBundle\Exceptions\FormNotSubmittedException;
use AppBundle\Exceptions\FormNotValidException;
use AppBundle\RequestConverter\JsonStringConverter;
use AppBundle\Routing\ResponseLeftHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use test\Functional\ReduceTest;
use Widmogrod\Monad\Either\Either;
use function foo\func;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

abstract class AbstractTransformer
{
    protected function __construct()
    {
    }

    public static function create(): AbstractTransformer
    {
        return new static();
    }

    public function transformLazy(): callable
    {
        return function (array $in): Either {
            return $this->transform(...$in);
        };
    }

    public function transform(FormInterface $form, Request $request)
    {
        return JsonStringConverter::convertJsonStringToArray($request)
                ->either(
                    static function (\Exception $exception): Either
                    {
                        return left($exception);
                    },
                    function () use ($form, $request): Either {
                        $form->handleRequest($request);

                        if (!$form->isSubmitted()) {
                            return left(new FormNotSubmittedException());
                        }

                        if (!$form->isValid()) {
                            return left(new FormNotValidException());
                        }

                        return $this->doTransform($form);
                    }
                );
    }

    abstract protected function doTransform(FormInterface $form): Either;
}
