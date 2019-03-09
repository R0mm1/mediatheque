<?php

namespace App\Repository;


use App\Specification\Specification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function match(Specification $specification, array $aOrder = [])
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $expr = $specification->match($qb, $this->getAlias());

        foreach ($aOrder as $field => $order) {
            $qb->addOrderBy($this->getAlias() . '.' . $field, $order);
        }

        $query = $qb->where($expr)->getQuery();

        $specification->modifyQuery($query);

        return $query->getResult();
    }

    abstract protected function getAlias(): string;

}