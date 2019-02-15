<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:38
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class OrX implements Specification
{
    /**
     * @var Specification[]
     */
    private $children;

    public function __construct(array $aSpecification)
    {
        $this->children = $aSpecification;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Query\Expr\Orx
     */
    public function match(QueryBuilder $qb, string $alias): Query\Expr\Orx
    {
        $aExpr = [];
        foreach ($this->children as $child) {
            $aExpr[] = $child->match($qb, $alias);
        }
        return call_user_func_array([$qb->expr(), 'orX'], $aExpr);
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
        foreach ($this->children as $child) {
            $child->modifyQuery($query);
        }
    }
}