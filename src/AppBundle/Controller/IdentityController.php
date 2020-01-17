<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use AppBundle\Exceptions\NotOfTheSameTypeException;
use AppBundle\RequestConverter\JsonStringConverter;
use AppBundle\RequestConverter\EventSubscriber;
use AppBundle\Exceptions\CannotDeleteIdentityException;
use AppBundle\Exceptions\EntityNotFoundException;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\ResponseLeftHandler;
use AppBundle\Routing\Transformer\IdentityTransformer;
use AppBundle\Service\EntityPersister;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityPersister
     */
    private $entityPersister;

    private $entityManager;

    public function __construct(
        EntityPersister $entityPersister,
        EntityManagerInterface $entityManager
    ) {
        $this->entityPersister = $entityPersister;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/identities", name="get-identities", methods={"GET"})
     */
    public function getIdentitiesAction(): JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $identities) {
                if (!$identities) {
                    return new Left(new EntityNotFoundException());
                }

                return new Right($identities);
            },
            map(
                static function (array $identities) {
                    return new Right($identities);
                }
            )
        )(
            $this
                ->entityManager
                ->getRepository(AbstractIdentity::class)
                ->findAll()
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return JsonResponse::create(
                        [
                            $either->extract(),
                        ]
                    );
                }
            );
    }

    /**
     * @Route("/api/identities/{identity}", name="delete-identities", methods={"DELETE"})
     * @param AbstractIdentity $identity
     * @return JsonResponse
     */
    public function deleteIdentitiesAction(AbstractIdentity $identity): JsonResponse
    {
        /** @var Either $tryCatch */
        $tryCatch = $this->entityPersister->buildDelete()($identity);
        $result = $tryCatch->either(
            static function () {
                return new Left(CannotDeleteIdentityException::create());
            },
            static function () {
                return new Right('Identity deleted. ');
            }
        );
        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (string $message) {
                    return JsonResponse::create($message);
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
        JsonStringConverter::convertJsonStringToArray($request);

        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()->transform(...$in);
            },
            bind(
                function (AbstractIdentity $identity) {
                    $this->entityPersister->buildSave()($identity);
                    return new Right($identity);
                }
            )
        )(
            [
                $this
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
                static function (AbstractIdentity $identity) {
                    return JsonResponse::create(
                        [
                            'posted' => $identity
                        ]
                    );
                }
            );
    }

    /**
     * @Route("/api/identities/{identity}", name="put-identities", methods={"PUT"})
     * @param AbstractIdentity $identity
     * @param Request $request
     * @return JsonResponse
     */
    public function putIdentitiesAction(AbstractIdentity $identity, Request $request): JsonResponse
    {
        JsonStringConverter::convertJsonStringToArray($request);

        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()->transform(...$in);
            },
            bind(
                static function (AbstractIdentity $identityUpdated) use ($identity): Either {
                    if (get_class($identityUpdated) !== get_class($identity)) {
                        return new Left(NotOfTheSameTypeException::create());
                    }

                    return new Right(
                        match(
                            [
                                LegalIdentity::class => static function (LegalIdentity $identityUpdated) use ($identity
                                ) {
                                    return $identity->updateIdentity($identityUpdated);
                                },
                                NaturalIdentity::class => static function (NaturalIdentity $identityUpdated) use (
                                    $identity
                                ) {
                                    return $identity->updateIdentity($identityUpdated);
                                },
                            ],
                            $identityUpdated
                        )
                    );
                }
            ),
            bind(
                function (AbstractIdentity $identity) {
                    $this->entityPersister->buildUpdate()($identity);
                    return new Right('Identity updated');
                }
            )
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
            static function (string $message): JsonResponse {
                return JsonResponse::create(
                    [
                        $message
                    ]
                );
            }
        );
    }

    /**
     * @Route("/api/identities/{identity}", name="get-identity", methods={"GET"})
     * @param AbstractIdentity $identity
     * @return JsonResponse
     */
    public function getIdentityAction(AbstractIdentity $identity): JsonResponse
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
                    return new Right($identity);
                }
            )
        )(
            [
                $this
                    ->entityManager
                    ->getRepository(AbstractIdentity::class)
                    ->find($identity),
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return JsonResponse::create(
                        [
                            $either->extract()
                        ]
                    );
                }
            );
    }
}
