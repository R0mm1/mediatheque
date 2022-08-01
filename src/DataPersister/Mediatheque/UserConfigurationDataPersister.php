<?php

namespace App\DataPersister\Mediatheque;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Mediatheque\UserConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserConfigurationDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private TokenStorageInterface  $tokenStorage,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserConfiguration;
    }

    /**
     * @var $data UserConfiguration
     */
    public function persist($data, array $context = [])
    {
        $data->setUser(
            $this->tokenStorage->getToken()->getUser()
        );
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        if ($data instanceof UserConfiguration && $data->getUser() === $this->tokenStorage->getToken()->getUser()) {
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        }
    }
}
