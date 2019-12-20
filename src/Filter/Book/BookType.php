<?php


namespace App\Filter\Book;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\ElectronicBook;
use App\Entity\PaperBook;
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

        $class = $value === self::OPT_PAPER ? PaperBook::class : ElectronicBook::class;
        $classMetadata = $queryBuilder->getEntityManager()->getClassMetadata($class);

        $queryBuilder->where($queryBuilder->expr()->isInstanceOf($alias, ':bookTypeClass'))
            ->setParameter('bookTypeClass', $classMetadata);


        dump($queryBuilder->getQuery()->getSQL(), $queryBuilder->getQuery()->getParameters());
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
