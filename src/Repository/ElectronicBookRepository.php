<?php

namespace App\Repository;

use App\Entity\ElectronicBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ElectronicBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElectronicBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElectronicBook[]    findAll()
 * @method ElectronicBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectronicBookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ElectronicBook::class);
    }
}
