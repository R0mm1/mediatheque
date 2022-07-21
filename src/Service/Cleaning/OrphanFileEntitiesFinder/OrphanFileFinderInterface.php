<?php

namespace App\Service\Cleaning\OrphanFileEntitiesFinder;

interface OrphanFileFinderInterface
{
    public function find(): array;

    public static function getType(): string;
}
