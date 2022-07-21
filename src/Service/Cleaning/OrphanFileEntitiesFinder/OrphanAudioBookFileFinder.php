<?php

namespace App\Service\Cleaning\OrphanFileEntitiesFinder;

use App\Entity\Book\AudioBook\File;
use Doctrine\ORM\EntityManagerInterface;

class OrphanAudioBookFileFinder implements OrphanFileFinderInterface
{
    use ResultFormatterTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function find(): array
    {
        return $this->formatResult(
            $this->entityManager->getRepository(self::getType())->findOrphans()
        );
    }

    public static function getType(): string
    {
        return File::class;
    }
}
