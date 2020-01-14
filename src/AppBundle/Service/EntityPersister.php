<?php


namespace AppBundle\Service;


use Doctrine\ORM\EntityManagerInterface;

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
}
