<?php


namespace App\DataPersister;


use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\Mediatheque\File;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class BookDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageInterface       $storage,
        private readonly LoggerInterface        $logger
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if(get_class($operation) !== Delete::class || !$data instanceof Book){
            throw new \LogicException(sprintf(
                "Should not be used for something else than delete a %s",
                Book::class
            ));
        }

        $this->entityManager->remove($data);

        if ($data instanceof ElectronicBook && $data->getBookFile() instanceof File) {
            $filepath = $this->storage->resolvePath($data->getBookFile(), 'file');
            $this->deleteBookFile($filepath, $data);
        }

        if ($data instanceof AudioBook && $data->getBookFile() instanceof File) {
            $filepath = $this->storage->resolvePath($data->getBookFile(), 'file');
            $this->deleteBookFile($filepath, $data);
        }

        if ($data->getCover() instanceof File) {
            $filepath = $this->storage->resolvePath($data->getCover(), 'file');
            if (!is_file($filepath)) {
                $this->logger->error(sprintf(
                    "Trying to remove non-existing cover at path %s for book #%d %s",
                    $filepath,
                    $data->getId(),
                    $data->getTitle()
                ));
            } else {
                unlink($filepath);
            }
        }

        $this->entityManager->flush();
    }

    private function deleteBookFile(string $filepath, Book $book): void
    {
        if (!is_file($filepath)) {
            $this->logger->error(sprintf(
                "Trying to remove non-existing electronic book file at path %s for book #%d %s",
                $filepath,
                $book->getId(),
                $book->getTitle()
            ));
        } else {
            unlink($filepath);
        }
    }
}
