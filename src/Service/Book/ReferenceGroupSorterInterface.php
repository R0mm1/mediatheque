<?php

namespace App\Service\Book;

use App\Entity\Book\ReferenceGroup;

interface ReferenceGroupSorterInterface
{
    public function order(int $referenceGroupId, array $positions): ReferenceGroup;
}
