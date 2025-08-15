<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Notation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NotationDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly TokenStorageInterface  $tokenStorage,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = match (get_class($operation)) {
            Post::class => $this->create($data),
            Put::class => $this->update($data),
            Delete::class => $this->remove($data)
        };

        if($result instanceof Notation){
            return $result;
        }
    }

    private function create(Notation $notation): Notation
    {
        if (empty($notation->getUser())) {
            $notation->setUser(
                $this->tokenStorage->getToken()->getUser()
            );
        }
        $this->entityManager->persist($notation);
        $this->entityManager->flush();

        return $notation;
    }

    private function update(Notation $notation): Notation
    {
        if (!$this->belongsToCurrentUser($notation)) {
            return $notation;
        }

        return $this->create($notation);
    }

    private function remove(Notation $notation): void
    {
        if (!$this->belongsToCurrentUser($notation)) {
            return;
        }

        $this->entityManager->remove($notation);
        $this->entityManager->flush();
    }

    private function belongsToCurrentUser(Notation $notation): bool
    {
        return $notation->getUser() === $this->tokenStorage->getToken()->getUser();
    }
}
