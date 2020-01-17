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
                    return new Right($identities);
                }
            )
        )(
            [
                $this
                    ->get('doctrine.orm.default_entity_manager')
                    ->getRepository(AbstractIdentity::class)
                    ->findAll(),
            ]
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
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            function (array $in){
                return new Right($this->get('entity_persister')->buildDelete($in[0]));
            },
            map(
                static function (callable $func) {
                    return new Right($func());
                }
            )
        )(
            [
                $identity
            ]
        );

        return $result
            ->either(
                ResponseLeftHandler::handle(),
                static function (Either $either) {
                    return JsonResponse::create(
                        [
                            'deleted' => $either->extract()
                        ]
                    );
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
                dump($in[0]->getData());
                return IdentityTransformer::create()->transform(...$in);
            },
            bind(
                static function (AbstractIdentity $identity) {
                    return new Right($this->get('entity_persister')->buildSave($identity));
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
                static function (callable $func) {
                    return JsonResponse::create(
                        [
                            'posted' => $func(),
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
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()->transform(...$in);
            },
            map(
                static function (AbstractIdentity $identityUpdated) use ($identity): AbstractIdentity {
                    return match(
                        [
                            LegalIdentity::class => static function (LegalIdentity $identityUpdated) use ($identity) {
                                return $identity->updateIdentity($identityUpdated);
                            },
                            NaturalIdentity::class => static function (NaturalIdentity $identityUpdated) use ($identity) {
                                return $identity->updateIdentity($identityUpdated);
                            },
                        ],
                        $identityUpdated
                    );
                }
            ),
            bind(
                static function (AbstractIdentity $identity) {
                    return new Right($this->get('entity_persister')->buildUpdate($identity));
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
            static function (Either $either): JsonResponse {
                return JsonResponse::create(
                    [
                        'updated' => $either->extract()()
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
                    ->get('doctrine.orm.default_entity_manager')
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
