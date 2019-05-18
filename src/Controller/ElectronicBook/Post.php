<?php


namespace App\Controller\ElectronicBook;


use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\ElectronicBook;

class Post extends AbstractCreateFile
{

    protected function getEntity(): \App\Entity\Mediatheque\FileInterface
    {
        return new ElectronicBook();
    }
}