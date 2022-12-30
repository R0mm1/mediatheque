<?php


namespace App\Controller\Book\AudioBookFile;


use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\AudioBook\File as AudioBookFile;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\ORM\EntityManagerInterface;

class Post extends AbstractCreateFile
{
    public function __construct(
        EntityManagerInterface                     $entityManager,
        ValidatorInterface                         $validator,
        ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
    )
    {
        parent::__construct($entityManager, $validator, $resourceMetadataFactory);
    }

    protected function getEntity(): FileInterface
    {
        return new AudioBookFile();
    }
}
