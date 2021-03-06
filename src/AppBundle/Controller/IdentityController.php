<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Exceptions\NotOfTheSameTypeException;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\Transformer\IdentityTransformer;
use AppBundle\Service\EntityPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\pipeline;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

class IdentityController extends AbstractController
{
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
        return self::buildResponse(
            $this
                ->entityManager
                ->getRepository(AbstractIdentity::class)
                ->findAll(),
            Response::HTTP_OK
            );
    }

    /**
     * @Route("/api/identities/{identity}", name="get-identity", methods={"GET"})
     */
    public function getIdentityAction(AbstractIdentity $identity): JsonResponse
    {
        return self::buildResponse(
            $identity,
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/api/identities/{identity}", name="delete-identities", methods={"DELETE"})
     */
    public function deleteIdentitiesAction(AbstractIdentity $identity): JsonResponse
    {
        return self::handleEither(
            $this->entityPersister->buildDelete()($identity)
        );
    }

    /**
     * @Route("/api/identities", name="post-identities", methods={"POST"})
     */
    public function postIdentitiesAction(Request $request): JsonResponse
    {
        return self::handleEither(
            pipeline(
                IdentityTransformer::create()->transformLazy(),
                bind($this->entityPersister->buildSave()())
            )(
                $this->sendForm($request, IdentityFormType::class, Request::METHOD_POST)
            )
        );
    }

    /**
     * @Route("/api/identities/{identity}", name="put-identities", methods={"PUT"})
     */
    public function putIdentitiesAction(AbstractIdentity $identity, Request $request): JsonResponse
    {
        return self::handleEither(
            pipeline(
                IdentityTransformer::create()->transformLazy(),
                bind(
                    static function (AbstractIdentity $identityUpdated) use ($identity): Either {
                        return $identityUpdated->getType() !== $identity->getType()
                            ? left(
                                NotOfTheSameTypeException::create(
                                    'Trying to update a '.$identityUpdated->getType().' identity with '.$identity->getType().' identity data'
                                )
                            )
                            : right($identity->updateIdentity($identityUpdated));
                    }
                ),
                bind($this->entityPersister->buildUpdate())
            )(
                $this->sendForm($request, IdentityFormType::class, Request::METHOD_PUT)
            )
        );
    }
}
