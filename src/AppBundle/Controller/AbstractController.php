<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\Event;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\ResponseLeftHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
