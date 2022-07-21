<?php

namespace App\Service\Cleaning\OrphanFileEntitiesFinder;

use App\Entity\Mediatheque\File;

trait ResultFormatterTrait
{
    protected function formatResult(array $entities): array
    {
        return array_map(function (File $file) {
            return new OrphanFileEntityDto(
                $file->getId(),
                $file->getPath(),
                $file->getUpdatedAt()
            );
        }, $entities);
    }
}
