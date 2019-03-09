<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:49
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class Order implements Specification
{
    private $field;
    private $order;

    public function __construct($field, $order)
    {
        $this->field = $field;
        $this->order = $order;
    }


    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Query\Expr
     */
    public function match(QueryBuilder $qb, string $alias): Query\Expr
    {
        $qb->addOrderBy($alias . $this->field, $this->order);
        return $qb->expr();
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
    }
}