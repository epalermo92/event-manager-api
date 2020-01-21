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

    public function buildSave(): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->persist($object);
                $this->entityManager->flush();
                return $object;
            },
            identity
        );
    }

    public function buildUpdate(): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->flush();
                return $object;
            },
            identity
        );
    }

    public function buildDelete(): callable
    {
        return tryCatch(
            function (object $object) {
                $this->entityManager->remove($object);
                $this->entityManager->flush();

                return $object;
            },
            identity
        );
    }
}
