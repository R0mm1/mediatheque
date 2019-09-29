<?php


namespace App\Filter\Book;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class AuthorFullName extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'authorFullname') return;

        $queryBuilder->join($queryBuilder->getRootAlias().'.authors', 'a')
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("a.firstname", ':authorSearchValue'),
                $queryBuilder->expr()->like("a.lastname", ':authorSearchValue')
            ))
            ->setParameter(':authorSearchValue', "%$value%");
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'authorFullname' => [
                'property' => 'authorFullname',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Make a search on both the firstname and the lastname of the book authors"
                ]
            ]
        ];
    }
}
