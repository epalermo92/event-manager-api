<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractController extends Controller
{
    protected static function buildResponse(): JsonResponse
    {
        
    }

    protected static function handleEither(): void
    {

    }
}
