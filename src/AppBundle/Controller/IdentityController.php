<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
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
use function Widmogrod\Useful\match;

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
                        ->buildDelete($identity);

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
                return IdentityTransformer::create()->transform(...$in);
            },
            bind($this->get('entity_persister')->buildSave())
        )(
            [
                $form = $this
                    ->createForm(
                        IdentityFormType::class,
                        null,
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
     * @Route("/api/identities/{identity}", name="put-identities", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function putIdentitiesAction(AbstractIdentity $identity, Request $request): JsonResponse
    {
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()->transform(...$in);
            },
            map(
                static function (AbstractIdentity $identityNew) use ($identity): AbstractIdentity {
                    return match(
                        [
                            LegalIdentity::class => function (LegalIdentity $identityNew) use ($identity) {
                                return $identity->updateIdentity($identityNew);
                            },
                            NaturalIdentity::class => function (NaturalIdentity $identityNew) use ($identity) {
                                return $identity->updateIdentity($identityNew);
                            },
                        ],
                        $identityNew
                    );
                }
            ),
            bind($this->get('entity_persister')->buildUpdate())
        )(
            [
                $this
                    ->createForm(
                        IdentityFormType::class,
                        null,
                        ['method' => Request::METHOD_PUT]
                    ),
                $request
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (AbstractIdentity $identity): JsonResponse {
                return JsonResponse::create(['updated' => $identity]);
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
