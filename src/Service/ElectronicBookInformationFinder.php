<?php

namespace App\Service;

use App\Entity\Book\ElectronicBookInformation\Book;
use App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation;
use App\Service\ElectronicBookInformationFinder\EpubFinderInterface;

class ElectronicBookInformationFinder implements ElectronicBookInformationFinderInterface
{
    public function __construct(
        protected EpubFinderInterface $epubFinder
    )
    {
    }

    public function find(Book $book): ElectronicBookInformation
    {
        //Done like this if someday we handle something else than epub format. In this case, the kind of finder to use
        //should be determined here.
        return $this->epubFinder->find($book);
    }
}
