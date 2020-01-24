<?php declare(strict_types=1);

namespace AppBundle\RequestConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

class JsonStringConverter
{
    public static function convertJsonStringToArray(Request $request): Either
    {
        if ($request->getContentType() !== 'json' || !$request->getContent()) {
            return right(true);
        }
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
             return left(new BadRequestHttpException('invalid json body: '.json_last_error_msg()));
        }
        $request
            ->request
            ->replace(is_array($data) ? $data : array());
        return right(true);
    }
}
