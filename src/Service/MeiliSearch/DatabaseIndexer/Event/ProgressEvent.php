<?php

namespace App\Service\MeiliSearch\DatabaseIndexer\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ProgressEvent extends Event
{
    public const NAME = 'meilisearch:databaseIndexer:progress';

    public function __construct(
        private readonly string $indexedElementType,
        private readonly int    $count,
        private readonly int    $over
    )
    {
    }

    public function getIndexedElementType(): string
    {
        return $this->indexedElementType;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getOver(): int
    {
        return $this->over;
    }


}
