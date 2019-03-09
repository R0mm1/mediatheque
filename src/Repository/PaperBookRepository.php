<?php

namespace App\Repository;

use App\Entity\PaperBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaperBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaperBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaperBook[]    findAll()
 * @method PaperBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaperBookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaperBook::class);
    }

//    /**
//     * @return PaperBook[] Returns an array of PaperBook objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaperBook
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
