<?php

namespace App\Repository\Book;

use App\Entity\Book\ReferenceGroup;
use App\Repository\AbstractRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReferenceGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReferenceGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReferenceGroup[]    findAll()
 * @method ReferenceGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReferenceGroupRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReferenceGroup::class);
    }

    protected function getAlias(): string
    {
        return 'g';
    }
}
