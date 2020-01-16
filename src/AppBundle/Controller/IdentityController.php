<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use AppBundle\Exceptions\FormNotSubmittedException;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\IdentityTransformer;
use AppBundle\Service\EntityPersister;
use Doctrine\ORM\EntityNotFoundException;
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
    private $entityPersister;

    public function __construct(
        EntityPersister $entityPersister
    ) {
        $this->entityPersister = $entityPersister;
    }

    /**
     * @Route("/api/identities", name="get-identities", methods={"GET"})
     */
    public function getIdentitiesAction(): JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in) {
                return new Right($in[0]->findAll());
            },
            bind(
                static function (array $identities) {
                    if (!$identities) {
                        return new Left(new EntityNotFoundException());
                    }

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
                $this->entityPersister->getRepository(AbstractIdentity::class),
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (JsonResponse $response) {
                return $response;
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
                if (!$in[0]->find($in[1])) {
                    return new Left(new EntityNotFoundException());
                }
                return new Right($in[0]->find($in[1]));
            },
            map(
                function (?object $identity) {
                    $this->entityPersister->delete($identity);
                    return new Right(
                        JsonResponse::create(
                            [
                                'deleted' => $identity,
                            ]
                        )
                    );
                }
            )
        )(
            [
                $this->entityPersister->getRepository(AbstractIdentity::class),
                $id,
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (Either $either) {
                return $either->extract();
            }
        );
        //TODO Why do it returns property id: null???
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
            bind(
                function (AbstractIdentity $identity): Either {
                    $this->entityPersister->save($identity);

                    return new Right($identity->getId());
                }
            )
        )(
            [
                $form = $this->createForm(
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

        return $result->either(
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
     * @Route("/api/identities/put/{id}", name="put-identities")
     * @param Request $request
     * @return JsonResponse
     */
    public function putIdentitiesAction($id, Request $request): JsonResponse
    {
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                $identityToUpdate = $in[2]->find($in[3]);
                if (!$identityToUpdate) {
                    return new Left(EntityNotFoundException::class);
                }

                $identity = IdentityTransformer::create()->transform($in[0], $in[1]);

                if ($identity instanceof Left){
                    return new Left(FormNotSubmittedException::create());
                }

                return new Right([
                    $identityToUpdate,
                    $identity,
                ]);
            },
            bind(
                function (array $in) {
                    /** @var NaturalIdentity $identityToUpdate */
                    $identityToUpdate = $in[0];
                    $identity = $in[1];
                    $identityToUpdate->updateIdentity($identity->extract());
                    $this->entityPersister->getManager()->flush();
                    return new Right(JsonResponse::create(
                        [
                            'updated' => $identityToUpdate
                        ]
                    ));

                }
            )
        )(
            [
                $this->createForm(
                    IdentityFormType::class,
                    [
                        'name' => 'Cristiano',
                        'surname' => 'Ronaldo',
                        'type' => 'natural',
                        'codice' => 'CTNRND88R24D352F',
                    ],
                    ['method' => Request::METHOD_POST]
                ),
                $request,
                $this->entityPersister->getManager()->getRepository(AbstractIdentity::class),
                $id
            ]
        );
        return $result->either(
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
                return new Right($in[0]->find($in[1]));
            },
            bind(
                static function (?object $identity) {
                    if (!$identity) {
                        return new Left(new EntityNotFoundException());
                    }

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
                $this->entityPersister->getRepository(AbstractIdentity::class),
                $id,
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (JsonResponse $response) {
                return $response;
            }
        );
    }
}
