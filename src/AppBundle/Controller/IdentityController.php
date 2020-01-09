<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AbstractIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IdentityController extends Controller
{
    /**
     * @Route("/identities_list", name="identities-list")
     */
    public function list()
    {
        $identitiesRepository = $this->getDoctrine()->getRepository(AbstractIdentity::class);
        $identities = $identitiesRepository->findAll();

        return JsonResponse::create($identities);
    }
}
