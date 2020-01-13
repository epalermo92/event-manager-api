<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Routing\Transformer\IdentityTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Right;

class IdentityController extends Controller
{
    /**
     * @Route("/identities_list", name="identities-list")
     * @return JsonResponse
     */
    public function list():JsonResponse
    {
        $identitiesRepository = $this->getDoctrine()->getRepository(AbstractIdentity::class);
        $identities = $identitiesRepository->findAll();

        return JsonResponse::create($identities);
    }

    /**
     * @Route("/identities_delete/{id}", name="identities-delete")
     * @return JsonResponse
     */
    public function deleteIdentity($id): JsonResponse
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
     * @Route("/identity_create", name="identity-create")
     * @return JsonResponse
     */
    public function createIdentity(): JsonResponse
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
     * @Route("/identity_update/{id}", name="identity-update")
     * @param string $id
     * @return JsonResponse
     */
    public function updateIdentity(string $id): JsonResponse
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
}
