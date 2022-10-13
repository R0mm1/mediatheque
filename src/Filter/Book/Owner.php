<?php

namespace App\Filter\Book;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class Owner extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
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
                'swagger' => [
                    'description' => "Filter books by owner. The value must be the id of the user, not the IRI."
                ]
            ]
        ];
    }
}
