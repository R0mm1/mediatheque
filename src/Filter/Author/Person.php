<?php

namespace App\Filter\Author;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Author;
use Doctrine\ORM\QueryBuilder;

class Person extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'person' || $resourceClass !== Author::class) return;

        $queryBuilder
            ->where(
                $queryBuilder->expr()->eq(
                    $queryBuilder->getRootAliases()[0] . '.person',
                    ':personId'
                )
            )
            ->setParameter('personId', $value, 'uuid');

    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'person' => [
                'property' => 'person',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Found the author matching the person id"
                ]
            ]
        ];
    }
}
