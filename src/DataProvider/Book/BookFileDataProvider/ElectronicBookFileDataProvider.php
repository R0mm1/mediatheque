<?php


namespace App\DataProvider\Book\BookFileDataProvider;


use App\Entity\Book\ElectronicBook\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vich\UploaderBundle\Storage\StorageInterface;

class ElectronicBookFileDataProvider extends AbstractBookFileDataProvider
{
    public function __construct(private StorageInterface $storage, private EntityManagerInterface $entityManager)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return File::class === $resourceClass &&
            //Check on $context['resource_class'] required to avoid errors on electronic book denormalization
            $context['resource_class'] === File::class &&
            parent::supports($resourceClass, $operationName, $context);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): BinaryFileResponse
    {
        $bookFile = $this->entityManager->getRepository(File::class)->find($id);

        $path = $this->storage->resolvePath($bookFile, 'file');

        $electronicBook = $bookFile->getElectronicBook();
        $bookTitle = $bookFile->getPath();
        if (is_object($electronicBook)) {
            $bookTitle = $electronicBook->getTitle();
        }

        return $this->createResponse($path, $bookTitle);
    }
}
