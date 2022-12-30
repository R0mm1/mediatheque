<?php


namespace App\DataProvider\Book\BookFileDataProvider;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book\ElectronicBook\File;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class ElectronicBookFileDataProvider extends AbstractBookFileDataProvider
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProviderInterface $itemProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if(!empty($context['deserialization_path'])){
            //The provider should return a BinaryFileResponse only if the file is requested in the first place, not
            //when it's a nested deserialization.
            return $this->itemProvider->provide($operation, $uriVariables, $context);
        }

        $bookFile = $this->entityManager->getRepository(File::class)->find($uriVariables['id']);

        $path = $this->storage->resolvePath($bookFile, 'file');

        $electronicBook = $bookFile->getElectronicBook();
        $bookTitle = $bookFile->getPath();
        if (is_object($electronicBook)) {
            $bookTitle = $electronicBook->getTitle();
        }

        return $this->createResponse($path, $bookTitle);
    }
}
