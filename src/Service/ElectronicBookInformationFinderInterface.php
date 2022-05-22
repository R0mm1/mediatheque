<?php

namespace App\Service;

use App\Entity\Book\ElectronicBookInformation\Book;
use App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation;

interface ElectronicBookInformationFinderInterface
{
    public function find(Book $book): ElectronicBookInformation;
}
