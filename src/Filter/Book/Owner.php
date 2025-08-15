<?php

namespace App\Filter\Book;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class Owner extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'owner') return;

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq(
                    $queryBuilder->getRootAliases()[0] . '.owner',
                    ':ownerId'
                )
            )
            ->setParameter('ownerId', $value, 'uuid');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'owner' => [
                'property' => 'owner',
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => "Filter books by owner. The value must be the id of the user, not the IRI."
                ]
            ]
        ];
    }
}
