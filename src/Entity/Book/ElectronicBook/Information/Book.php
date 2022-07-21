<?php

namespace App\Entity\Book\ElectronicBook\Information;

use App\Entity\Mediatheque\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Book\ElectronicBook\Information\Book")
 * @ORM\Table(name="electronic_book_information_book")
 * @Vich\Uploadable
 */
class Book extends File
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"book_create"})
     * @Vich\UploadableField(mapping="electronicBookInformation_book", fileNameProperty="path")
     * @Assert\File(
     *     mimeTypes = {"application/zip", "application/epub+zip"}
     * )
     */
    protected ?HttpFile $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation", mappedBy="bookFile")
     */
    protected $electronicBookInformation;

    /**
     * @return mixed
     */
    public function getElectronicBookInformation()
    {
        return $this->electronicBookInformation;
    }

    /**
     * @param mixed $electronicBookInformation
     * @return Book
     */
    public function setElectronicBookInformation($electronicBookInformation)
    {
        $this->electronicBookInformation = $electronicBookInformation;
        return $this;
    }
}
