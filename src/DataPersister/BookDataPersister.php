<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Book;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\Mediatheque\File;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class BookDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected StorageInterface       $storage,
        protected LoggerInterface        $logger
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Book &&
            isset($context['item_operation_name']) &&
            $context['item_operation_name'] === 'delete';
    }

    /**
     * {@inheritdoc}
     */
    public function persist($data, array $context = [])
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
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
