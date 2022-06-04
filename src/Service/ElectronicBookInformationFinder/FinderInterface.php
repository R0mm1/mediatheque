<?php

namespace App\Service\ElectronicBookInformationFinder;

use App\Entity\Book\ElectronicBook\Information\Book;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;

interface FinderInterface
{
    public function find(Book $book): ElectronicBookInformation;
}
