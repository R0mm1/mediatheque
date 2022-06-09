<?php

namespace App\Entity\Book\ElectronicBook;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="electronic_book_file_download_token")
 */
class FileDownloadToken extends \App\Entity\Mediatheque\FileDownloadToken
{
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBook\File")
     * @Groups({"mediatheque_fileDownloadToken:set"})
     */
    private File $file;

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): FileDownloadToken
    {
        $this->file = $file;
        return $this;
    }
}
