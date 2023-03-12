<?php


namespace App\DataProvider\Book;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Book;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookRawFileDataProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $book = $this->entityManager->getRepository($operation->getClass())->find($uriVariables['id']);

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
