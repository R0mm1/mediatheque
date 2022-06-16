<?php

namespace App\Service;

use App\Entity\Book\ElectronicBook\Information\Book;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;

interface ElectronicBookInformationFinderInterface
{
    public function find(Book $book): ElectronicBookInformation;
}
