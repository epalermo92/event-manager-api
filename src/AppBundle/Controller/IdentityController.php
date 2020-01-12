<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Routing\FormType\IdentityFormType;
use AppBundle\Routing\Transformer\IdentityTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Widmogrod\Monad\Either\Left;
use function Widmogrod\Monad\Either\left;

class IdentityController extends Controller
{
    /**
     * @Route("/identities_list", name="identities-list")
     */
    public function list():JsonResponse
    {
        $identitiesRepository = $this->getDoctrine()->getRepository(AbstractIdentity::class);
        $identities = $identitiesRepository->findAll();

        return JsonResponse::create($identities);
    }

    /**
     * @Route("/identities_delete/{id}", name="identities-delete")
     * @return JsonResponse|Left
     */
    public function deleteIdentity($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $identity = $entityManager->getRepository(AbstractIdentity::class)->find($id);

        if (!$identity)
        {
            return left(new NotFoundHttpException('Identity not found'));
        }

        $entityManager->remove($identity);
        $entityManager->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }

    /**
     * @Route("/identity_create", name="identity-create")
     * @param IdentityFormType $form
     * @return JsonResponse|\Widmogrod\Monad\Either\Either
     */
    public function createIdentity()
    {
        $form = new IdentityFormType();
        $form['name'] = 'Pippo';
        $form['surname'] = 'Topolino';
        $form['codiceFiscale'] = 'PPOTLN45T56U527G';

        if (!IdentityTransformer::isValid($form))
        {
            return IdentityTransformer::create($form)->DoTransformInvalid();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $newIdentity = IdentityTransformer::create($form)->DoTransformValid();

        $entityManager->persist($newIdentity);
        $entityManager->flush();

        return JsonResponse::create([
            'result' => true
        ]);
    }
}
