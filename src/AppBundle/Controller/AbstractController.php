<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Routing\ResponseLeftHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Widmogrod\Monad\Either\Either;

class AbstractController extends Controller
{
    /**
     * @param object<Event,AbstractIdentity> $responseData
     * @param $status
     * @return JsonResponse
     */
    protected static function buildResponse($responseData, $status): JsonResponse
    {
        return JsonResponse::create(
            [
                $responseData,
                $status
            ]
        );
    }

    protected static function handleEither(Either $r): JsonResponse
    {
        return $r->either(
            ResponseLeftHandler::handle(),
            static function ($object) {
                return self::buildResponse(
                    $object,
                    Response::HTTP_OK
                );
            }
        );
    }

    protected function sendForm(Request $request, string $class, string $method): array
    {
        return [
            $this
                ->createForm(
                    $class,
                    null,
                    ['method' => $method]
                ),
            $request,
        ];
    }
}
