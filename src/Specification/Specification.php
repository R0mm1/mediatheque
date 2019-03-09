<?php

namespace App\Specification;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface Specification
{
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     */
    public function match(QueryBuilder $qb, string $alias);

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query);
}
