<?php

namespace App\Service\MeiliSearch\DatabaseIndexer;

interface DatabaseIndexerInterface
{
    public function index(int $batchSize): void;
}
