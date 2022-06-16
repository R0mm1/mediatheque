<?php


namespace App\Filter\Book;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\AudioBook\Book as AudioBook;
use Doctrine\ORM\QueryBuilder;

class BookType extends AbstractFilter
{
    const OPT_ALL = 'all';
    const OPT_PAPER = 'paper';
    const OPT_ELECTRONIC = 'electronic';
    const OPT_AUDIO = 'audio';
    const OPTIONS = [
        self::OPT_ALL => 'all', self::OPT_PAPER => 'paper', self::OPT_ELECTRONIC => 'electronic', self::OPT_AUDIO => 'audio'
    ];

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'bookType' || $value === self::OPTIONS[self::OPT_ALL]) return;

        $alias = $queryBuilder->getRootAliases()[0];

        $class = match ($value){
            self::OPT_PAPER => PaperBook::class,
            self::OPT_ELECTRONIC => ElectronicBook::class,
            self::OPT_AUDIO => AudioBook::class,
            default => throw new \Exception("Invalid type ".$value)
        };

        $classMetadata = $queryBuilder->getEntityManager()->getClassMetadata($class);

        $queryBuilder->andWhere($queryBuilder->expr()->isInstanceOf($alias, ':bookTypeClass'))
            ->setParameter('bookTypeClass', $classMetadata);
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
