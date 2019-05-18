<?php


namespace App\Controller\Book;


use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\Cover;
use App\Entity\Mediatheque\FileInterface;

class CreateCover extends AbstractCreateFile
{

    protected function getEntity(): FileInterface
    {
        return new Cover();
    }
}