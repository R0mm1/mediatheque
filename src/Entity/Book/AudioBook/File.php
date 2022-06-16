<?php

namespace App\Entity\Book\AudioBook;

use App\Entity\Mediatheque\File as BaseFile;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="audio_book_file")
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
class File extends BaseFile
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="book_audioBook", fileNameProperty="path")
     * @Assert\File(
     *     mimeTypes = {"application/zip", "audio/mpeg"},
     *     )
     */
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\AudioBook\Book", mappedBy="bookFile")
     */
    protected ?Book $audioBook;

    public function getAudioBook(): ?Book
    {
        return $this->audioBook;
    }

    public function setAudioBook(?Book $audioBook): self
    {
        $this->audioBook = $audioBook;

        return $this;
    }
}
