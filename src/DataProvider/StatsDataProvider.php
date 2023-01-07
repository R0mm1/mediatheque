<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Resources\Stats;
use App\Service\StatsService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StatsDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly StatsService $statsService
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $stats = (new Stats())->setId(time());
        $statsData = [];

        foreach (array_keys($this->requestStack->getCurrentRequest()->query->all()) as $requestStat) {
            $statsData[$requestStat] = match ($requestStat) {
                'authorsCount' => $this->statsService->getAuthorsCount(),
                'booksCount' => $this->statsService->getBooksCount(),
                'authorsBooksDistribution' => $this->statsService->getAuthorsBooksDistribution(),
                default => throw new BadRequestHttpException("Invalid stat $requestStat")
            };
        }

        $stats->setStats($statsData);
        return [$stats];
    }
}
