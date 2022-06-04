<?php

namespace App\DataProvider\ElectronicBookInformation;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Book\ElectronicBook\Information\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageFileDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        protected string $projectDir,
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $image = $this->entityManager->find($resourceClass, $id);

        if(!$image instanceof Image){
            throw new NotFoundHttpException('Not Found');
        }

        $fullPath = sprintf(
            '%s/%s',
            $this->projectDir,
            $image->getPath()
        );

        if(!is_readable($fullPath)){
            throw new \Exception(sprintf(
                "The file related to the image #%s could not be found at %s",
                $image->getId(),
                $fullPath
            ));
        }

        return new BinaryFileResponse($fullPath);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Image::class && $operationName === 'get_file';
    }
}
