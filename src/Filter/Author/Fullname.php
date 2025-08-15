<?php


namespace App\Filter\Author;


use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Author;
use Doctrine\ORM\QueryBuilder;

class Fullname extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'fullname' || $resourceClass !== Author::class) return;

        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.person', 'p')
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("p.firstname", ':authorSearchValue'),
                $queryBuilder->expr()->like("p.lastname", ':authorSearchValue')
            ))
            ->setParameter('authorSearchValue', "%$value%");
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
