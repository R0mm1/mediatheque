<?php

namespace App\DataPersister\Mediatheque;

use ApiPlatform\Exception\ItemNotFoundException;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Mediatheque\UserConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserConfigurationDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly TokenStorageInterface  $tokenStorage,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof UserConfiguration) {
            throw new \LogicException(sprintf(
                "This processor should not be used with something else than %s",
                UserConfiguration::class
            ));
        }

        match (get_class($operation)) {
            Post::class, Put::class => $this->persist($data),
            Delete::class => $this->remove($data),
            default => throw new \LogicException("This processor should not be used with an other operation than Post, Put or Delete")
        };
    }

    private function persist(UserConfiguration $data)
    {
        $data->setUser(
            $this->tokenStorage->getToken()->getUser()
        );
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    private function remove(UserConfiguration $data)
    {
        if ($data->getUser() === $this->tokenStorage->getToken()->getUser()) {
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        }
    }
}
