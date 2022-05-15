<?php


namespace App\Controller\Book\BookFile;


use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\BookFile;
use App\Entity\Mediatheque\FileInterface;

class Post extends AbstractCreateFile
{
    protected function getEntity(): FileInterface
    {
        return new BookFile();
    }
}
