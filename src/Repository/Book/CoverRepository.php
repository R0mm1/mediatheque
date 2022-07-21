<?php

namespace App\Repository\Book;

use App\Entity\Book\Cover;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cover|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cover|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cover[]    findAll()
 * @method Cover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoverRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cover::class);
    }

    protected function getAlias(): string
    {
        return 'bc';
    }

    public function findOrphans()
    {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);
        $qb->leftJoin($alias . '.book', 'b')
            ->where($qb->expr()->isNull('b.id'));
        return $qb->getQuery()->getResult();
    }
}
