<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Book\BookFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Vich\UploaderBundle\Storage\StorageInterface;

class ElectronicBookFileDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    protected $storage;
    protected $entityManager;

    public function __construct(StorageInterface $storage, EntityManagerInterface $entityManager)
    {
        $this->storage = $storage;
        $this->entityManager = $entityManager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        //Check on $context['resource_class'] required to avoid errors on electronic book denormalization
        return BookFile::class === $resourceClass && $operationName === 'get' && $context['resource_class'] === BookFile::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $bookFile = $this->entityManager->getRepository(BookFile::class)->find($id);

        $path = $this->storage->resolvePath($bookFile, 'file');

        $electronicBook = $bookFile->getElectronicBook();
        $bookTitle = $bookFile->getPath();
        if (is_object($electronicBook)) {
            $bookTitle = $electronicBook->getTitle();
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $bookTitle . '.epub');

        return $response;
    }
}
