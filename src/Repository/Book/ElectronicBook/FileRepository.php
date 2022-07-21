<?php

namespace App\Repository\Book\ElectronicBook;

use App\Entity\Book\ElectronicBook\File;
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
        return 'bebf';
    }

    public function findOrphans()
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);
        $qb->leftJoin($alias . '.electronicBook', 'beb')
            ->where($qb->expr()->isNull('beb.id'));
        return $qb->getQuery()->getResult();
    }
}
