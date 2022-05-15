<?php

namespace App\Entity\Mediatheque;

use Symfony\Component\HttpFoundation\File\File as HttpFile;

interface FileInterface
{
    function getFile(): ?HttpFile;

    function setFile(?HttpFile $file): void;
}