<?php

namespace App\Repository\Book\AudioBook;

use App\Entity\Book\AudioBook\File;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

class FileRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    protected function getAlias(): string
    {
        return 'babf';
    }

    public function findOrphans()
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);
        $qb->leftJoin($alias . '.audioBook', 'bab')
            ->where($qb->expr()->isNull('bab.id'));
        return $qb->getQuery()->getResult();
    }
}
