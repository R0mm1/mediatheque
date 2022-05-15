<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Resources\Stats;
use App\Service\StatsService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StatsDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private StatsService $statsService
    )
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null)
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

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Stats::class && $operationName === 'get';
    }
}
