<?php


namespace App\Service;


use App\Entity\Author;
use App\Entity\Book;
use App\Entity\ElectronicBook;
use App\Entity\PaperBook;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    /**
     * @var AuthorRepository
     */
    protected $authorsRepository;

    /**
     * @var BookRepository
     */
    protected $booksRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->authorsRepository = $entityManager->getRepository(Author::class);
        $this->booksRepository = $entityManager->getRepository(Book::class);
    }

    public function getAuthorsCount()
    {
        return $this->authorsRepository->count([]);
    }

    public function getBooksCount()
    {
        $getBookTypeResult = function ($class) {
            $qb = $this->booksRepository->createQueryBuilder('b');
            $query = $qb
                ->select('COUNT(b.id)')
                ->where($qb->expr()->isInstanceOf('b', ':bookTypeClass'))
                ->setParameter('bookTypeClass', $qb->getEntityManager()->getClassMetadata($class))
                ->getQuery();
            $result = $query->getSingleResult();

            return (int)array_pop($result);
        };
        $return = [
            'paper' => $getBookTypeResult(PaperBook::class),
            'electronic' => $getBookTypeResult(ElectronicBook::class)
        ];
        $return['total'] = $return['paper'] + $return['electronic'];
        return $return;
    }

    public function getAuthorsBooksDistribution()
    {
        $booksCount = $this->booksRepository->count([]);

        $distributions = $this->authorsRepository->createQueryBuilder('a')
            ->select('a.id', 'a.firstname', 'a.lastname', 'count(b) as booksCount')
            ->leftJoin('a.books', 'b')
            ->groupBy('a')
            ->getQuery()
            ->getArrayResult();

        foreach ($distributions as &$distribution) {
            $distribution['booksCountPercent'] = round(($distribution['booksCount'] / $booksCount) * 100, 1);
        }
        return $distributions;
    }
}
