<?php

namespace App\DataPersister\Mediatheque;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Mediatheque\FileDownloadToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class FileDownloadTokenDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private readonly TokenGeneratorInterface $uriSafeTokenGenerator,
        private readonly EntityManagerInterface  $entityManager,
        private readonly TokenStorageInterface   $tokenStorage
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof FileDownloadToken;
    }

    public function persist($data, array $context = [])
    {
        if (!$data instanceof FileDownloadToken) {
            throw new \LogicException("Data should be an instance of " . FileDownloadToken::class);
        }

        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof User) {
            throw new \LogicException("User must be logged in");
        }

        $data->setToken($this->uriSafeTokenGenerator->generateToken());
        $data->setUser($user);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        throw new \LogicException("Should not be reached");
    }
}
