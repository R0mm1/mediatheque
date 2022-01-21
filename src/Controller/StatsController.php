<?php


namespace App\Controller;


use App\Service\StatsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StatsController
{
    public function getStats(Request $request, StatsService $statsService)
    {
        $stats = [];
        foreach (array_keys($request->query->all()) as $requestStat) {
            switch ($requestStat) {
                case 'authorsCount':
                    $stats[$requestStat] = $statsService->getAuthorsCount();
                    break;
                case 'booksCount':
                    $stats[$requestStat] = $statsService->getBooksCount();
                    break;
                case 'authorsBooksDistribution':
                    $stats[$requestStat] = $statsService->getAuthorsBooksDistribution();
                    break;
                default:
                    throw new BadRequestHttpException("Invalid stat $requestStat");
            }
        }
        return new JsonResponse($stats);
    }
}
