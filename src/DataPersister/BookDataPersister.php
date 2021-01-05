<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Book;
use App\Entity\ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class BookDataPersister implements ContextAwareDataPersisterInterface
{
    protected $entityManager;
    protected $storage;

    public function __construct(EntityManagerInterface $entityManager, StorageInterface $storage)
    {
        $this->entityManager = $entityManager;
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Book &&
            isset($context['item_operation_name']) &&
            in_array($context['item_operation_name'], ['delete']);
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

        if ($data instanceof ElectronicBook && is_object($data->getBookFile())) {
            $filepath = $this->storage->resolvePath($data->getBookFile(), 'file');
            unlink($filepath);
        }
    }
}
