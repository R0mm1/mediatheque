<?php

namespace App\Entity\Book\ElectronicBook;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\DataPersister\Mediatheque\FileDownloadTokenDataPersister;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="electronic_book_file_download_token")
 */
#[ApiResource(
    shortName: 'ElectronicBookFileDownloadToken',
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new Post(
            processor: FileDownloadTokenDataPersister::class
        )
    ],
    normalizationContext: ['groups' => ['mediatheque_fileDownloadToken:get']],
    denormalizationContext: ['groups' => ['mediatheque_fileDownloadToken:set']]
)]
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
