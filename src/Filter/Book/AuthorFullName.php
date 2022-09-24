<?php


namespace App\Filter\Book;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Book;
use Doctrine\ORM\QueryBuilder;

class AuthorFullName extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'authorFullname' || $resourceClass !== Book::class) return;

        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.authors', 'a')
            ->join('a.person', 'p')
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("p.firstname", ':authorSearchValue'),
                $queryBuilder->expr()->like("p.lastname", ':authorSearchValue')
            ))
            ->setParameter('authorSearchValue', "%$value%");
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
