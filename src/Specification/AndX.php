<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:32
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class AndX implements Specification
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
     * @return Query\Expr\Andx
     */
    public function match(QueryBuilder $qb, string $alias): Query\Expr\Andx
    {
        $aExpr = [];
        foreach ($this->children as $child) {
            $aExpr[] = $child->match($qb, $alias);
        }
        return call_user_func_array([$qb->expr(), 'andX'], $aExpr);
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