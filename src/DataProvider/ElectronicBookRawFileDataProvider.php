<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Book\ElectronicBook\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ElectronicBookRawFileDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Book::class === $resourceClass && $operationName === 'get_file_raw';
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $electronicBook = $this->entityManager->getRepository(Book::class)->find($id);
        $file = $electronicBook->getBookFile();

        if (!is_object($file)) {
            throw new NotFoundHttpException('Not Found');
        }

        return $file;
    }
}
