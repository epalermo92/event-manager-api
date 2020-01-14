<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Routing\Transformer\IdentityTransformer;
use AppBundle\Service\DatabaseManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Right;

class IdentityController extends Controller
{
    private $databaseManager;

    public function __construct(
        DatabaseManager $databaseManager
    )
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @Route("/api/identities/get", name="get-identities", methods={"GET"})
     */
    public function getIdentitiesAction():JsonResponse
    {
        $identitiesRepository = $this->getDoctrine()->getRepository(AbstractIdentity::class);
        $identities = $identitiesRepository->findAll();

        return JsonResponse::create($identities);
    }

    /**
     * @Route("/api/identities/delete/{id}", name="delete-identities", methods={"DELETE"})
     */
    public function deleteIdentitiesAction($id): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $identity = $entityManager->getRepository(AbstractIdentity::class)->find($id);

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
     * @Route("/api/identities/post", name="post-identities", methods={"POST"})
     */
    public function postIdentitiesAction(): JsonResponse
    {
        $form = $this->createFormBuilder()
            ->add('type', TextType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('codice', TextType::class)
            ->getForm();

        $form->get('type')->setData('natural');
        $form->get('name')->setData('Pippo');
        $form->get('surname')->setData('Topolino');
        $form->get('codice')->setData('PPOTLN45T56U527G');

        $entityManager = $this->getDoctrine()->getManager();
        $newIdentity = IdentityTransformer::transform($form);

        if ($newIdentity instanceof Right)
        {
            $entityManager->persist($newIdentity->extract());
            $entityManager->flush();

            return JsonResponse::create([
                'result' => true
            ]);
        }

        return JsonResponse::create([
            'result' => false
            ]);
    }

    /**
     * @Route("/api/identities/put/{id}", name="put-identities", methods={"PUT"})
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
     * @Route("/api/identities/get/{id}", name="get-identity", methods={"GET"})
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
