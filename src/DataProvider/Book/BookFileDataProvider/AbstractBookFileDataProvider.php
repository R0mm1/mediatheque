<?php

namespace App\DataProvider\Book\BookFileDataProvider;

use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractBookFileDataProvider  implements ProviderInterface
{
    protected function createResponse(string $filePath, string $bookTitle): BinaryFileResponse
    {
        $file = new File($filePath);

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $bookTitle . '.' . $file->getExtension());

        return $response;
    }
}
