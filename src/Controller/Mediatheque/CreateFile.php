<?php

namespace App\Controller\Mediatheque;

use App\Entity\Mediatheque\File;
use App\Entity\Mediatheque\FileInterface;

class CreateFile extends AbstractCreateFile
{
    protected function getEntity(): FileInterface
    {
        return new File();
    }
}