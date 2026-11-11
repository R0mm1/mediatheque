<?php

namespace App\DataPersister\ElectronicBookInformation;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * @implements ProcessorInterface<ElectronicBookInformation, void>
 */
class ElectronicBookInformationDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly StorageInterface       $storage,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    private function remove(ElectronicBookInformation $data): void
    {
        $filePath = $this->storage->resolvePath($data->getBookFile(), 'file');
        $pathinfo = pathinfo($filePath);
        $extractedDirPath = sprintf(
            '%s/%s_extracted',
            $pathinfo['dirname'],
            $pathinfo['filename']
        );

        $filesystem = new Filesystem();
        $filesystem->remove([$filePath, $extractedDirPath]);

        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$data instanceof ElectronicBookInformation && $operation->getClass() !== Delete::class) {
            throw new \LogicException(sprintf(
                "This processor should be used only to delete %s",
                ElectronicBookInformation::class
            ));
        }

        $this->remove($data);
    }
}
