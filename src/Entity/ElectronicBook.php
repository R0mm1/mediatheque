<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ElectronicBookRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class ElectronicBook extends Book
{
    /**
     * @var \App\Entity\Mediatheque\File|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Book\BookFile", cascade={"remove", "persist"}, inversedBy="electronicBook")
     * @Groups({"book:get", "book:set"})
     */
    private $bookFile;

//    /**
//     * @Groups({"book:get"})
//     */
    private $hasBookFile;

    /**
     * @return Mediatheque\File|null
     */
    public function getBookFile(): ?Mediatheque\File
    {
        return $this->bookFile;
    }

    /**
     * @param Mediatheque\File|null $bookFile
     * @return $this
     */
    public function setBookFile(?Mediatheque\File $bookFile): self
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
