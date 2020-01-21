<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Routing\ResponseLeftHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Widmogrod\Monad\Either\Either;

class AbstractController extends Controller
{
    /**
     * @param object<Event,AbstractIdentity> $responseData
     * @return JsonResponse
     */
    protected static function buildResponse($responseData): JsonResponse
    {
        return !$responseData
            ? JsonResponse::create()
            :JsonResponse::create(
                [
                    $responseData
                ]
            );
    }

    protected static function handleEither(Either $r): JsonResponse
    {
        return $r->either(
            ResponseLeftHandler::handle(),
            static function ($object) {
                return self::buildResponse($object);
            }
        );
    }
}
