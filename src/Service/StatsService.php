<?php


namespace App\Service;


use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    protected AuthorRepository $authorsRepository;

    protected BookRepository $booksRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->authorsRepository = $entityManager->getRepository(Author::class);
        $this->booksRepository = $entityManager->getRepository(Book::class);
    }

    public function getAuthorsCount(): int
    {
        return $this->authorsRepository->count([]);
    }

    public function getBooksCount(): array
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
            'electronic' => $getBookTypeResult(ElectronicBook::class),
            'audio' => $getBookTypeResult(AudioBook::class)
        ];
        $return['total'] = $return['paper'] + $return['electronic'] + $return['audio'];
        return $return;
    }

    public function getAuthorsBooksDistribution(): array
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
