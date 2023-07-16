<?php

namespace App\Service\MeiliSearch\Indexer;

use App\Normalizer\MeilisearchNormalizer;
use App\Service\MeiliSearch\ClientFactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class Indexer implements IndexerInterface
{
    public function __construct(
        private readonly ClientFactoryInterface $clientFactory,
        private readonly SerializerInterface    $serializer
    )
    {
    }

    /**
     * @throws IndexationFailedException
     */
    public function index(array $data): void
    {
        $client = $this->clientFactory->createClient();
        $index = $client->index('mediatheque');
        $task = $index->addDocumentsJson(
            $this->serializer->serialize(
                $data,
                MeilisearchNormalizer::FORMAT,
                ['groups' => 'meilisearch']
            )
        );

        $result = $index->waitForTask($task['taskUid']);

        if ($result['status'] !== 'succeeded') {
            if (!array_key_exists('error', $result) ||
                !is_array($result['error']) ||
                !array_key_exists('message', $result['error']) ||
                !is_string($result['error']['message'])
            ) {
                throw new \RuntimeException("Could not retrieve error message from Meilisearch response");
            }

            throw new IndexationFailedException($result['error']['message']);
        }
    }
}
