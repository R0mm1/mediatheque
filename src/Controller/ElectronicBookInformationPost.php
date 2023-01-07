<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use App\Entity\Book\ElectronicBook\Information\Book;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;
use App\Service\ElectronicBookInformationFinderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ElectronicBookInformationPost
{
    public function __construct(
        protected ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        protected ValidatorInterface                         $validator,
        protected EntityManagerInterface                     $entityManager,
        protected ElectronicBookInformationFinderInterface   $electronicBookInformationFinder
    )
    {
    }

    public function __invoke(Request $request)
    {
        $electronicBookFile = $request->files->get('electronicBook');

        if (!$electronicBookFile) {
            throw new BadRequestHttpException('"electronicBook" is required');
        }

        $book = new Book();
        $book->setFile($electronicBookFile);

        $resourceMetadata = $this->resourceMetadataFactory->create(ElectronicBookInformation::class);

        $it = $resourceMetadata->getIterator();
        while ($it->valid()) {
            $metadata = $it->current();
            foreach ($metadata->getOperations() ?? [] as $operation) {
                if ($operation->getMethod() === 'POST') {
                    $validationGroups = $operation->getValidationContext();
                }
            }
            $it->next();
        }

        if (isset($validationGroups) && isset($validationGroups['groups']) && is_array($validationGroups['groups'])) {
            $this->validator->validate($book, $validationGroups['groups']);
        }

        $this->entityManager->persist($book);
        $electronicBookInformation = $this->electronicBookInformationFinder->find($book);
        $electronicBookInformation->setBookFile($book);
        $this->entityManager->persist($electronicBookInformation);

        $this->entityManager->flush();

        return $electronicBookInformation;
    }
}
