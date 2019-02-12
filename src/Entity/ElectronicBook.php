<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ElectronicBookRepository")
 */
class ElectronicBook
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private $size;

    /**
     * @ORM\OneToOne(targetEntity="Book", mappedBy="electronicBook")
     */
    private $book;

    /**
     * @Assert\NotBlank
     * @Assert\File(
     *     mimeTypes = {"application/epub+zip"},
     *     mimeTypesMessage = "Veuillez sélectionner un fichier epub"
     * )
     */
    private $newFile;

    public function changeFile(File $filepath = null)
    {
        $this->newFile = $filepath;
        if (!is_null($filepath)) {
            $this->setFile($this->newFile->getFilename());
            $this->setMimeType($this->newFile->getMimeType());
            $this->setSize($this->newFile->getSize());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    private function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    private function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    private function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getBook()
    {
        return $this->book;
    }

    public function setBook($book): self
    {
        $this->book = $book;

        return $this;
    }

    public function asArray()
    {
        return [
            'file' => $this->getFile(),
            'size' => $this->getSize(),
            'mime' => $this->getMimeType()
        ];
    }
}
