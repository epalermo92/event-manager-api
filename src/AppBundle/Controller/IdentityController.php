<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Exceptions\NotOfTheSameTypeException;
use AppBundle\RequestConverter\JsonStringConverter;
use AppBundle\Exceptions\CannotDeleteIdentityException;
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
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\map;
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
                ->findAll());
    }

    /**
     * @Route("/api/identities/{identity}", name="get-identity", methods={"GET"})
     * @param AbstractIdentity $identity
     * @return JsonResponse
     */
    public function getIdentityAction(AbstractIdentity $identity): JsonResponse
    {
        return self::buildResponse($identity);
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
        return self::handleEither($tryCatch);
    }

    /**
     * @Route("/api/identities", name="post-identities", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postIdentitiesAction(Request $request): JsonResponse
    {
        $result = pipeline(
            IdentityTransformer::create()->transformLazy(),
            bind(
                function (AbstractIdentity $identity) {
                    $this->entityPersister->buildSave()($identity);
                    return right($identity);
                }
            )
        )(
            $this->sendForm($request, IdentityFormType::class, Request::METHOD_POST)
        );

        return self::handleEither($result);
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
            IdentityTransformer::create()->transformLazy(),
            bind(
                static function (AbstractIdentity $identityUpdated) use ($identity): Either {
                    return $identityUpdated->getType() !== $identity->getType()
                        ? left(NotOfTheSameTypeException::create())
                        : right($identity->updateIdentity($identityUpdated));
                }
            ),
            bind($this->entityPersister->buildUpdate())
        )(
                $this->sendForm($request, IdentityFormType::class, Request::METHOD_PUT)
        );

        return self::handleEither($result);
    }
}
