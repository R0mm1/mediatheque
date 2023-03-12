<?php

namespace App\Filter\Book\ReferenceGroup\Book;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class ReferenceGroup extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'referenceGroup') return;

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->eq(
                    $queryBuilder->getRootAliases()[0] . '.referenceGroup',
                    ':referenceGroupId'
                )
            )
            ->setParameter('referenceGroupId', $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'owner' => [
                'property' => 'referenceGroup',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Filter by the reference group. The value must be the id of the user, not the IRI."
                ]
            ]
        ];
    }
}
