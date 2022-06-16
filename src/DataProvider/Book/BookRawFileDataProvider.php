<?php


namespace App\DataProvider\Book;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Book;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookRawFileDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return in_array($resourceClass, [ElectronicBook::class, AudioBook::class]) && $operationName === 'get_file_raw';
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $book = $this->entityManager->getRepository($resourceClass)->find($id);

        if(!$book instanceof Book){
            throw new NotFoundHttpException('Not Found');
        }

        $file = $book->getBookFile();

        if (!is_object($file)) {
            throw new NotFoundHttpException('Not Found');
        }

        return $file;
    }
}
