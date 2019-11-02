<?php


namespace App\Filter\Book;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class BookType extends AbstractFilter
{
    const OPT_ALL = 'all';
    const OPT_PAPER = 'paper';
    const OPT_ELECTRONIC = 'electronic';
    const OPTIONS = [
        self::OPT_ALL => 'all', self::OPT_PAPER => 'paper', self::OPT_ELECTRONIC => 'electronic'
    ];

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'bookType' || $value === self::OPTIONS[self::OPT_ALL]) return;

        $alias = $queryBuilder->getRootAliases()[0];

        if ($value === self::OPTIONS[self::OPT_PAPER]) {
            $queryBuilder->where($queryBuilder->expr()->isNotNull("$alias.paperBook"));
        } else if ($value === self::OPTIONS[self::OPT_ELECTRONIC]) {
            $queryBuilder->where($queryBuilder->expr()->isNotNull("$alias.electronicBook"));
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'bookType' => [
                'property' => 'bookType',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Filter book by type (" . implode(', ', self::OPTIONS) . ")"
                ]
            ]
        ];
    }
}
