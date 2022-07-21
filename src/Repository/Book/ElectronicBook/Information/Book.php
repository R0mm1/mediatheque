<?php

namespace App\Repository\Book\ElectronicBook\Information;

use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class Book extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, \App\Entity\Book\ElectronicBook\Information\Book::class);
    }

    protected function getAlias(): string
    {
        return 'bebib';
    }

    public function findOrphans()
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);
        $qb->leftJoin($alias . '.electronicBookInformation', 'bebiebi')
            ->where($qb->expr()->isNull('bebiebi.id'));
        return $qb->getQuery()->getResult();
    }
}
