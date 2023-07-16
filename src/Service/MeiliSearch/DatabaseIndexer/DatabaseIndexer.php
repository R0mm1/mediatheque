<?php

namespace App\Service\MeiliSearch\DatabaseIndexer;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Service\MeiliSearch\DatabaseIndexer\Event\ErrorEvent;
use App\Service\MeiliSearch\DatabaseIndexer\Event\ProgressEvent;
use App\Service\MeiliSearch\Indexer\IndexationFailedException;
use App\Service\MeiliSearch\Indexer\IndexerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DatabaseIndexer implements DatabaseIndexerInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly IndexerInterface         $indexer,
        private readonly EntityManagerInterface   $entityManager
    )
    {
    }

    public function index(int $batchSize): void
    {
        $this->indexType(Book::class, $batchSize);

        $this->indexType(Author::class, $batchSize);
    }

    private function indexType(string $entityFqdn, int $batchSize): void
    {
        $done = 0;
        $repository = $this->entityManager->getRepository($entityFqdn);

        $total = $repository->count([]);

        while ($done < $total) {
            $books = $repository->findBy(
                [],
                ['id' => 'ASC'],
                $batchSize,
                $done
            );

            $done += $batchSize;

            try {
                $this->indexer->index($books);
            } catch (\Throwable $exception) {
                $errorEvent = new ErrorEvent(
                    $entityFqdn,
                    $exception->getMessage()
                );
                $this->eventDispatcher->dispatch($errorEvent, ErrorEvent::NAME);

                continue;
            }

            $event = new ProgressEvent(
                $entityFqdn,
                min($done, $total),
                $total
            );

            $this->eventDispatcher->dispatch($event, ProgressEvent::NAME);
        }
    }
}
