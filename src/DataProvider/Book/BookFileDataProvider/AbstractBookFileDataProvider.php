<?php

namespace App\DataProvider\Book\BookFileDataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractBookFileDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $operationName === 'get'
            && (isset($context['item_operation_name']) && $context['item_operation_name'] === 'get');
    }

    protected function createResponse(string $filePath, string $bookTitle): BinaryFileResponse
    {
        $file = new File($filePath);

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $bookTitle . '.' . $file->getExtension());

        return $response;
    }
}
