<?php declare(strict_types=1);

namespace AppBundle\Routing;

use AppBundle\Exceptions\CannotDeleteIdentityException;
use AppBundle\Exceptions\EntityNotBuiltException;
use AppBundle\Exceptions\EntityNotFoundException;
use AppBundle\Exceptions\FormNotSubmittedException;
use AppBundle\Exceptions\FormNotValidException;
use AppBundle\Exceptions\NotOfTheSameTypeException;
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
            FormNotSubmittedException::class => static function() {
                return JsonResponse::create([
                    'Exception' => FormNotSubmittedException::create()->getMessage()
                ]);
            },
            EntityNotFoundException::class => static function() {
                return JsonResponse::create([
                    'Exception' => EntityNotFoundException::create()->getMessage()
                ]);
            },
            EntityNotBuiltException::class => static function() {
                return JsonResponse::create([
                    'Exception' => EntityNotBuiltException::create()->getMessage()
                ]);
            },
            CannotDeleteIdentityException::class => static function() {
                return JsonResponse::create([
                    'Exception' => CannotDeleteIdentityException::create()->getMessage()
                ]);
            },
            NotOfTheSameTypeException::class => static function() {
                return JsonResponse::create([
                    'Exception' => NotOfTheSameTypeException::create()->getMessage()
                ]);
            },
            any => reThrow
        ]);
    }
}
