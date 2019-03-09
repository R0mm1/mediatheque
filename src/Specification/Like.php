<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:26
 */

namespace App\Specification;


use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class Like implements Specification
{
    CONST STRICT_NONE = 0;
    CONST STRICT_LEFT = 1;
    CONST STRICT_RIGHT = 2;
    CONST STRICT_BOTH = 3;

    private $field;
    private $value;
    private $strict;

    public function __construct(string $field, string $value, int $strict = self::STRICT_NONE)
    {
        $this->field = $field;
        $this->value = $value;
        $this->strict = $strict;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Expr\Comparison
     */
    public function match(QueryBuilder $qb, string $alias): Expr\Comparison
    {
        $value = $this->value;
        switch ($this->strict) {
            case self::STRICT_LEFT:
                $value = "$value%";
                break;
            case self::STRICT_RIGHT:
                $value = "%$value";
                break;
            case self::STRICT_NONE:
                $value = "%$value%";
                break;
        }


        $qb->setParameter($this->field, $value);
        return $qb->expr()->like($alias . '.' . $this->field, ':' . $this->field);
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
    }
}