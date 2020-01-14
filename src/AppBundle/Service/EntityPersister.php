<?php declare(strict_types=1);

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class EntityPersister
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(object $object): void
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    public function delete(object $object): void
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }

    public function getRepository(string $class): ObjectRepository
    {
        return $this->entityManager->getRepository($class);
    }
}
