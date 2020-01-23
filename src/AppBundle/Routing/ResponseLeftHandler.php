<?php declare(strict_types=1);

namespace AppBundle\Routing;

use AppBundle\Exceptions\CannotDeleteIdentityException;
use AppBundle\Exceptions\EntityNotBuiltException;
use AppBundle\Exceptions\EntityNotFoundException;
use AppBundle\Exceptions\FormNotSubmittedException;
use AppBundle\Exceptions\FormNotValidException;
use AppBundle\Exceptions\NotOfTheSameTypeException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Widmogrod\Useful\match;
use const Widmogrod\Functional\reThrow;
use const Widmogrod\Useful\any;

class ResponseLeftHandler
{
    private static function buildCreateJsonResponse(): callable
    {
        return static function (\Exception $exception): JsonResponse {
            return JsonResponse::create(
                [
                    'error' => $exception->getMessage(),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        };
    }

    private const badExceptionMap = [
        FormNotValidException::class,
        FormNotSubmittedException::class,
        EntityNotFoundException::class,
        EntityNotBuiltException::class,
        CannotDeleteIdentityException::class,
        NotOfTheSameTypeException::class,
        UniqueConstraintViolationException::class,
    ];

    public static function handle(): callable
    {
        return match(
            array_merge(
                array_reduce(
                    self::badExceptionMap,
                    static function (array $carry, string $eClass): array {
                        return array_merge($carry, [$eClass => self::buildCreateJsonResponse()]);
                    },
                    []
                ),
                [any => reThrow]
            )
        );
    }
}
