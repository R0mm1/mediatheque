<?php

namespace App\Filter\Book\Notation;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class User extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'user.id') return;

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq(
                    $queryBuilder->getRootAliases()[0] . '.user',
                    ':userId'
                )
            )
            ->setParameter('userId', $value, 'uuid');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'owner' => [
                'property' => 'user',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Filter book notation by user. The value must be the id of the user, not the IRI."
                ]
            ]
        ];
    }
}
