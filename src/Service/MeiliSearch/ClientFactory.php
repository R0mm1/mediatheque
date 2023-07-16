<?php

namespace App\Service\MeiliSearch;

use Meilisearch\Client;

final class ClientFactory implements ClientFactoryInterface
{
    public function __construct(
        private readonly string $meilisearchMasterKey,
        private readonly string $meilisearchInternalBaseUrl
    )
    {
    }

    public function createClient(): Client
    {
        return new Client($this->meilisearchInternalBaseUrl, $this->meilisearchMasterKey);
    }

}
