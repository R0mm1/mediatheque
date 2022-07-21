<?php

namespace App\Service\Cleaning\OrphanFileEntitiesFinder;

class OrphanFileEntityDto
{
    public function __construct(
        private readonly null|int|string $id,
        private readonly null|string     $path,
        private readonly null|\DateTime $creationDate
    )
    {
    }

    public function getId(): null|int|string
    {
        return $this->id;
    }

    public function getPath(): null|string
    {
        return $this->path;
    }

    public function getCreationDate(): null|\DateTime
    {
        return $this->creationDate;
    }
}
