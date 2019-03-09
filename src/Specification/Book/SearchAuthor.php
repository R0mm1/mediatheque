<?php
/**
 * Created by PhpStorm.
 * User: romain3
 * Date: 11/02/19
 * Time: 17:44
 */

namespace App\Specification\Book;


use App\Specification\Specification;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SearchAuthor implements Specification
{
    private $searchString;

    public function __construct($searchString)
    {
        $this->searchString = $searchString;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return Query\Expr\Orx
     */
    public function match(QueryBuilder $qb, string $alias): Query\Expr\Orx
    {
        $authorAlias = 'a';
        $qb
            ->leftJoin("$alias.authors", $authorAlias)
            ->setParameter('authorSearch', '%' . $this->searchString . '%');

        return $qb->expr()->orX(
            $qb->expr()->like("$authorAlias.firstname", ':authorSearch'),
            $qb->expr()->like("$authorAlias.lastname", ':authorSearch')
        );
    }

    /**
     * @param Query $query
     * @return mixed
     */
    public function modifyQuery(Query $query)
    {
    }
}