<?php

namespace App\Service\MeiliSearch;

use Meilisearch\Client;

interface ClientFactoryInterface
{
    public function createClient(): Client;
}
