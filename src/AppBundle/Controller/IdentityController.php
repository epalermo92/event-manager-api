<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\Transformer\IdentityTransformer;
use AppBundle\Service\EntityPersister;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Either;
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
        return JsonResponse::create(
            $this->entityPersister->getRepository(AbstractIdentity::class)->findAll()
        );
    }

    /**
     * @Route("/api/identities/{id}", name="delete-identities", methods={"DELETE"})
     */
    public function deleteIdentitiesAction($id): JsonResponse
    {
        $identity = $this->entityPersister->getRepository(AbstractIdentity::class)->find($id);

        if (!$identity)
        {
            return JsonResponse::create([
                'result' => 'Identity not found in database'
            ]);
        }

        $entityManager->remove($identity);
        $entityManager->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/api/identities", name="post-identities", methods={"POST"})
     */
    public function postIdentitiesAction(Request $request): JsonResponse
    {
        /** @var Either $result */
        $result = pipeline(
            static function (array $in): Either {
                return IdentityTransformer::create()->transform(...$in);
            },
            bind(
                static function (AbstractIdentity $identity):Either {
                    $this->entityPersister->save($identity);
                    return new Right($identity->getId());
                }
            )
        )([
            $form = $this->createForm(IdentityFormType::class, null, ['method' => Request::METHOD_POST]),
            $request
        ]);

        return $result->either(
            static function (\Exception $exception) {
                return JsonResponse::create([
                    'Exception' => $exception->getMessage()
                ]);
            },
            static function (int $id) {
                return JsonResponse::create([
                    'id' => $id
                ]);
            }
        );
    }

    /**
     * @Route("/api/identities/{id}", name="put-identities", methods={"PUT"})
     */
    public function putIdentitiesAction(string $id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $identityToUpdate = $entityManager->getRepository(AbstractIdentity::class)->find($id);

        if (!$identityToUpdate) {
            return JsonResponse::create([
                'result' => false
            ]);
        }

        $newIdentity = new LegalIdentity('Facile.it', '350789989');
        /** @var LegalIdentity $identityToUpdate */
        $identityToUpdate->updateIdentity($newIdentity);
        $entityManager->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/api/identities/{id}", name="get-identity", methods={"GET"})
     */
    public function getIdentityAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $identitiesRepository = $entityManager->getRepository(AbstractIdentity::class);
        $identity = $entityManager->getRepository(AbstractIdentity::class)->find($id);

        if (!$identity)
        {
            return JsonResponse::create([
                'result' => false
            ]);
        }

        return JsonResponse::create([
            'result' => $identity
        ]);
    }
}
