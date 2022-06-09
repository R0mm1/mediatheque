<?php


namespace App\DataProvider\Book\BookFileDataProvider;


use App\Entity\Book\AudioBook\File as AudioBookFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vich\UploaderBundle\Storage\StorageInterface;

class AudioBookFileDataProvider extends AbstractBookFileDataProvider
{
    public function __construct(
        private readonly StorageInterface $storage,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return AudioBookFile::class === $resourceClass &&
            //Check on $context['resource_class'] required to avoid errors on audio book denormalization
            $context['resource_class'] === AudioBookFile::class &&
            parent::supports($resourceClass, $operationName, $context);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): BinaryFileResponse
    {
        /**@var $bookFile AudioBookFile*/
        $bookFile = $this->entityManager->getRepository(AudioBookFile::class)->find($id);

        $path = $this->storage->resolvePath($bookFile, 'file');

        $audioBook = $bookFile->getAudioBook();
        $bookTitle = $bookFile->getPath();
        if (is_object($audioBook)) {
            $bookTitle = $audioBook->getTitle();
        }

        return $this->createResponse($path, $bookTitle);
    }
}
