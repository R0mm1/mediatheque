<?php


namespace App\Controller\Book\AudioBookFile;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\AudioBook\File as AudioBookFile;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\ORM\EntityManagerInterface;

class Post extends AbstractCreateFile
{
    public function __construct(
        EntityManagerInterface           $entityManager,
        ValidatorInterface               $validator,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
    )
    {
        parent::__construct($entityManager, $validator, $resourceMetadataFactory);
    }

    protected function getEntity(): FileInterface
    {
        return new AudioBookFile();
    }
}
