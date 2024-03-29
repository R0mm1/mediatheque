<?php

namespace App\Dto\Resources;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\DataProvider\StatsDataProvider;

#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new GetCollection(
            provider: StatsDataProvider::class
        )
    ]
)]
class Stats
{
    #[ApiProperty(identifier: true)]
    private int $id;

    /**@var string[] */
    private array $stats;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Stats
     */
    public function setId(int $id): Stats
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * @param string[] $stats
     * @return Stats
     */
    public function setStats(array $stats): Stats
    {
        $this->stats = $stats;
        return $this;
    }
}
