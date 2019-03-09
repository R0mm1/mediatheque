<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 18:07
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class None implements Specification
{

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Query\Expr\Comparison
     */
    public function match(QueryBuilder $qb, string $alias): Query\Expr\Comparison
    {
        return $qb->expr()->eq('1', '1');
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
    }
}