<?php

namespace App\Controller;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Book\ElectronicBookInformation\Book;
use App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation;
use App\Service\ElectronicBookInformationFinderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ElectronicBookInformationPost
{
    public function __construct(
        protected ResourceMetadataFactoryInterface         $resourceMetadataFactory,
        protected ValidatorInterface                       $validator,
        protected EntityManagerInterface                   $entityManager,
        protected ElectronicBookInformationFinderInterface $electronicBookInformationFinder
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

        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $resourceMetadata = $this->resourceMetadataFactory->create(ElectronicBookInformation::class);
        $validationGroups = $resourceMetadata->getOperationAttribute($attributes, 'validation_groups', null, true);

        $this->validator->validate($book, ['groups' => $validationGroups]);

        $this->entityManager->persist($book);
        $electronicBookInformation = $this->electronicBookInformationFinder->find($book);
        $electronicBookInformation->setBookFile($book);
        $this->entityManager->persist($electronicBookInformation);

        $this->entityManager->flush();

        return $electronicBookInformation;
    }
}
