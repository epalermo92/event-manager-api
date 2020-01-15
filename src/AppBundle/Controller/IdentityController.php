<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
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
use function Widmogrod\Functional\pipeline;

class IdentityController extends Controller
{
    private $entityPersister;

    public function __construct(
        EntityPersister $entityPersister
    )
    {
        $this->entityPersister = $entityPersister;
    }

    /**
     * @Route("/api/identities", name="get-identities", methods={"GET"})
     */
    public function getIdentitiesAction():JsonResponse
    {
        /** @var Either<\LogicException,JsonResponse> $result */
        $result = pipeline(
            function ($in){
                return $in[0]->findAll();
            },
            function ($identities){
                if (!$identities)
                {
                    return new Left(new EntityNotFoundException());
                }

                return new Right(JsonResponse::create(
                    [
                        $identities
                    ]
                ));
            }
        )(
            [
                $this->entityPersister->getRepository(AbstractIdentity::class)
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (JsonResponse $response){
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
        $identity = $this->entityPersister->getRepository(AbstractIdentity::class)->find($id);

        $this->entityPersister->delete($identity);

        return JsonResponse::create([
            'Deleted' => $identity
        ]);
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
     * @Route("/api/identities/{id}", name="put-identities", methods={"PUT"})
     * @param string $id
     * @return JsonResponse
     */
    public function putIdentitiesAction(string $id): JsonResponse
    {
        /** @var Either<LogicException,JsonResponse> $result */
        $result = pipeline(
            static function ($in){
                if ($in[0])
                {
                    $in[0]->updateIdentity($in[1]);
                    return new Right($in[0]);
                }
                return new Left(new EntityNotFoundException());
            },
            bind(
                function (LegalIdentity $identity){
                    $this->entityPersister->getManager()->flush();
                    return new Right(JsonResponse::create(
                        [
                            'updated' => $identity
                        ]
                    ));
                }
            )
        )
        (
            [
                $this->entityPersister->getRepository(AbstractIdentity::class)->find($id),
                new LegalIdentity('Facile.it', '350789989')
            ]
        );

        return $result->either(
            ResponseLeftHandler::handle(),
            static function (JsonResponse $response){
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
        $identity = $this->entityPersister->getRepository(AbstractIdentity::class)->find($id);

        return JsonResponse::create([
            'result' => $identity
        ]);
    }
}
