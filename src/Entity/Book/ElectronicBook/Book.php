<?php

namespace App\Entity\Book\ElectronicBook;

use App\Entity\Book as BaseBook;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="electronic_book")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Book extends BaseBook
{
    /**
     * @var File|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBook\File", cascade={"remove", "persist"}, inversedBy="electronicBook")
     * @Groups({"book:get", "book:set"})
     */
    private $bookFile;

//    /**
//     * @Groups({"book:get"})
//     */
    private $hasBookFile;

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

    /**
     * @return bool
     */
    public function getHasBookFile(): bool
    {
        return $this->hasBookFile;
    }

    /**
     * @param bool $hasBookFile
     * @return $this
     */
    public function setHasBookFile(bool $hasBookFile): self
    {
        $this->hasBookFile = $hasBookFile;

        return $this;
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->setHasBookFile(is_object($this->bookFile));
    }
}
