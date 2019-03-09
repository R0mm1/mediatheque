<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:23
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class Equal implements Specification
{
    private $field;
    private $value;

    public function __construct(string $field, string $value)
    {
        $this->field = $field;
        $this->value = $value;
    }


    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Expr\Comparison
     */
    public function match(QueryBuilder $qb, string $alias): Expr\Comparison
    {
        $qb->setParameter($this->field, $this->value);
        return $qb->expr()->eq($alias . $this->field, ':' . $this->field);
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
    }
}