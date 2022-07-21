<?php

namespace App\Service\Cleaning;

interface OrphanFileEntitiesFinderInterface
{
    public function find(): array;
}
