<?php

namespace App\DataPersister\Mediatheque;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Mediatheque\FileDownloadToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class FileDownloadTokenDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly TokenGeneratorInterface $uriSafeTokenGenerator,
        private readonly EntityManagerInterface  $entityManager,
        private readonly TokenStorageInterface   $tokenStorage
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof FileDownloadToken || get_class($operation) !== Post::class) {
            throw new \LogicException(sprintf(
                "This processor should be used only to post %s",
                FileDownloadToken::class
            ));
        }

        return $this->persist($data);
    }

    private function persist($data)
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

        return $data;
    }
}
