<?php declare(strict_types=1);

namespace AppBundle\Routing;

use AppBundle\Exceptions\FormNotValidException;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Widmogrod\Useful\match;
use const Widmogrod\Functional\reThrow;
use const Widmogrod\Useful\any;

class ResponseLeftHandler
{
    public static function handle(): callable
    {
        return match([
            FormNotValidException::class => static function() {
                return JsonResponse::create([
                    'Exception' => FormNotValidException::create()->getMessage()
                ]);
            },
            any => reThrow
        ]);
    }
}
