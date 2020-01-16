<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Exceptions\EntityNotBuiltException;
use AppBundle\Exceptions\EntityNotFoundException;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\IdentityTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\map;
use function Widmogrod\Functional\pipeline;

class IdentityController extends Controller
{
    /**
     * @Route("/api/identities", name="get-identities", methods={"GET"})
     */
    public function getIdentitiesAction(): JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in) {
                if (!$in[0]) {
                    return new Left(new EntityNotFoundException());
                }

                return new Right($in[0]);
            },
            map(
                static function (array $identities) {
                    return new Right(
                        JsonResponse::create(
                            [
                                $identities,
                            ]
                        )
                    );
                }
            )
        )(
            [
                $this
                    ->get('entity_persister')
                    ->getManager()
                    ->getRepository(AbstractIdentity::class)
                    ->findAll(),
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return $either->extract();
                }
            );
    }

    /**
     * @Route("/api/identities/{id}", name="delete-identities", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteIdentitiesAction($id): JsonResponse
    {
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in) {
                if (!$in[0]) {
                    return new Left(new EntityNotFoundException());
                }

                return new Right($in[0]);
            },
            map(
                function (AbstractIdentity $identity) {
                    $id = $identity
                        ->getId();
                    $this
                        ->get('entity_persister')
                        ->delete($identity);

                    return new Right(
                        JsonResponse::create(
                            [
                                'deleted' => 'Person with code '.$id,
                            ]
                        )
                    );
                }
            )
        )(
            [
                $this
                    ->get('entity_persister')
                    ->getManager()
                    ->getRepository(AbstractIdentity::class)
                    ->find($id),
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return $either->extract();
                }
            );
    }

    /**
     * @Route("/api/identities", name="post-identities", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postIdentitiesAction(Request $request): JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()
                    ->transform(...$in);
            },
            bind(
                function (AbstractIdentity $identity): Either {
                    $this
                        ->get('entity_persister')
                        ->save($identity);

                    return new Right($identity->getId());
                }
            )
        )(
            [
                $form = $this
                    ->createForm(
                        IdentityFormType::class,
                        [
                            'name' => 'Pippo',
                            'surname' => 'Topolino',
                            'codice' => 'PPPTLN33T13D122F',
                            'type' => 'natural',
                        ],
                        ['method' => Request::METHOD_POST]
                    ),
                $request,
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (int $id) {
                    return JsonResponse::create(
                        [
                            'id' => $id,
                        ]
                    );
                }
            );
    }

    /**
     * @Route("/api/identities/{id}", name="put-identities", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function putIdentitiesAction($id, Request $request): JsonResponse
    {
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()
                    ->transform($in[0], $in[1])
                    ->either(
                        static function () {
                            return new Left(EntityNotBuiltException::create());
                        },
                        static function (AbstractIdentity $identity) use ($in) {
                            if (!$in[2]) {
                                return new Left(EntityNotFoundException::create());
                            }

                            $in[2]
                                ->updateIdentity($identity);

                            return new Right($in[2]);
                        }
                    );
            },
            bind(
                function (AbstractIdentity $identity) {
                    $this
                        ->get('entity_persister')
                        ->getManager()
                        ->flush();

                    return new Right(
                        JsonResponse::create(
                            [
                                'updated' => $identity,
                            ]
                        )
                    );

                }
            )
        )(
            [
                $this
                    ->createForm(
                        IdentityFormType::class,
                        [
                            'name' => 'Cristiano',
                            'surname' => 'Ronaldo',
                            'type' => 'natural',
                            'codice' => 'CTNRND88R24D352F',
                        ],
                        ['method' => Request::METHOD_PUT]
                    ),
                $request,
                $this
                    ->get('entity_persister')
                    ->getManager()
                    ->getRepository(AbstractIdentity::class)
                    ->find($id),
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (JsonResponse $response) {
                    return $response;
                }
            );
    }

    /**
     * @Route("/api/identities/{id}", name="get-identity", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getIdentityAction($id): JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in) {
                if (!$in[0]) {
                    return new Left(EntityNotFoundException::create());
                }

                return new Right($in[0]);
            },
            map(
                static function (object $identity) {
                    return new Right(
                        JsonResponse::create(
                            [
                                $identity,
                            ]
                        )
                    );
                }
            )
        )(
            [
                $this
                    ->get('entity_persister')
                    ->getManager()
                    ->getRepository(AbstractIdentity::class)
                    ->find($id),
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return $either->extract();
                }
            );
    }
}
