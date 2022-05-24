<?php

namespace App\DataPersister\ElectronicBookInformation;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Storage\StorageInterface;

class ElectronicBookInformationDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        protected StorageInterface       $storage,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return
            get_class($data) === ElectronicBookInformation::class &&
            isset($context['item_operation_name']) &&
            $context['item_operation_name'] === 'delete';
    }

    public function persist($data, array $context = [])
    {
        throw new \Exception("Should not be reached");
    }

    /**
     * @param ElectronicBookInformation $data
     * @param array $context
     * @return void
     */
    public function remove($data, array $context = [])
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
}
