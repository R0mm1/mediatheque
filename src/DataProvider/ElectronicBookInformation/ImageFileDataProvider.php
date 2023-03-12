<?php

namespace App\DataProvider\ElectronicBookInformation;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book\ElectronicBook\Information\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageFileDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly string                 $projectDir,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $image = $this->entityManager->find($operation->getClass(), $uriVariables['id']);

        if (!$image instanceof Image) {
            throw new NotFoundHttpException('Not Found');
        }

        $fullPath = sprintf(
            '%s/%s',
            $this->projectDir,
            $image->getPath()
        );

        if (!is_readable($fullPath)) {
            throw new \Exception(sprintf(
                "The file related to the image #%s could not be found at %s",
                $image->getId(),
                $fullPath
            ));
        }

        return new BinaryFileResponse($fullPath);
    }
}
