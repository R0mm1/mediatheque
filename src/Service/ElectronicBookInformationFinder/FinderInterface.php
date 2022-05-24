<?php

namespace App\Service\ElectronicBookInformationFinder;

use App\Entity\Book\ElectronicBookInformation\Book;
use App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation;

interface FinderInterface
{
    public function find(Book $book): ElectronicBookInformation;
}
