<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserSelfDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
