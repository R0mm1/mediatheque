<?php

namespace App\Service\MeiliSearch\Indexer;

interface IndexerInterface
{
    public function index(array $data): void;
}
