<?php

namespace App\Service\Cleaning;

interface OrphanFilesFinderInterface
{
    public function find(): array;
}
