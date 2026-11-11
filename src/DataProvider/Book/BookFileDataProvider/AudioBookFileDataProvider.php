<?php


namespace App\DataProvider\Book\BookFileDataProvider;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book\AudioBook\File as AudioBookFile;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class AudioBookFileDataProvider extends AbstractBookFileDataProvider
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

        /**@var AudioBookFile $bookFile*/
        $bookFile = $this->entityManager->getRepository(AudioBookFile::class)->find($uriVariables['id']);

        $path = $this->storage->resolvePath($bookFile, 'file');

        $audioBook = $bookFile->getAudioBook();
        $bookTitle = $bookFile->getPath();
        if (is_object($audioBook)) {
            $bookTitle = $audioBook->getTitle();
        }

        return $this->createResponse($path, $bookTitle);
    }
}
