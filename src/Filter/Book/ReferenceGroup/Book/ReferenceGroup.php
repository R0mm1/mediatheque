<?php

namespace App\Filter\Book\ReferenceGroup\Book;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class ReferenceGroup extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
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
