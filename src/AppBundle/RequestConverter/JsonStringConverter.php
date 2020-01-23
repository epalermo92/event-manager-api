<?php declare(strict_types=1);

namespace AppBundle\RequestConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonStringConverter
{
    public static function convertJsonStringToArray(Request $request): void
    {
        if ($request->getContentType() !== 'json' || !$request->getContent()) {
            return;
        }
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: '.json_last_error_msg());
        }
        $request
            ->request
            ->replace(is_array($data) ? $data : array());
    }
}
