<?php

namespace App\Service\MeiliSearch\DatabaseIndexer\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ErrorEvent extends Event
{
    public const NAME = 'meilisearch:databaseIndexer:error';

    public function __construct(
        private readonly string $indexedElementType,
        private readonly string $message
    )
    {
    }

    public function getIndexedElementType(): string
    {
        return $this->indexedElementType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
