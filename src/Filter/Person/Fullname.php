<?php


namespace App\Filter\Person;


use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Person;
use Doctrine\ORM\QueryBuilder;

class Fullname extends AbstractFilter
{
    /**
     * Passes a property through the filter.
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'fullname' || $resourceClass !== Person::class) return;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("o.firstname", ':personSearchValue'),
                $queryBuilder->expr()->like("o.lastname", ':personSearchValue')
            ))
            ->setParameter('personSearchValue', "%$value%");
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'fullname' => [
                'property' => 'fullname',
                'type' => 'string',
                'required' => false,
                'openapi' => [
                    'description' => "Make a search on both the firstname and the lastname"
                ]
            ]
        ];
    }
}
