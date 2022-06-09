<?php

namespace App\Entity\Book\AudioBook;

use App\Entity\Book as BaseBook;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="audio_book")
 * @Vich\Uploadable
 */
class Book extends BaseBook
{
    /**
     * @var File|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Book\AudioBook\File", cascade={"remove", "persist"}, inversedBy="audioBook")
     * @Groups({"book:get", "book:set"})
     */
    private ?File $bookFile;

    /**
     * @return File|null
     */
    public function getBookFile(): ?File
    {
        return $this->bookFile;
    }

    /**
     * @param File|null $bookFile
     * @return $this
     */
    public function setBookFile(?File $bookFile): self
    {
        $this->bookFile = $bookFile;

        return $this;
    }
}
