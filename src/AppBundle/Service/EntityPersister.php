<?php declare(strict_types=1);

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use function Widmogrod\Monad\Either\tryCatch;
use const Widmogrod\Functional\identity;

class EntityPersister
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildSave(object $object): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->persist($object);
                $this->entityManager->flush();
            },
            identity,
            $object
        );
    }

    public function buildUpdate(object $object): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->flush();
            },
            identity,
            $object
        );
    }

    public function buildDelete(object $object): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->remove($object);
                $this->entityManager->flush();
            },
            identity,
            $object
        );
    }
}
