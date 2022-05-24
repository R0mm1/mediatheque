<?php

namespace App\Entity\Book\ElectronicBookInformation;

use App\Entity\Mediatheque\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
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
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBookInformation\ElectronicBookInformation", mappedBy="bookFile")
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
