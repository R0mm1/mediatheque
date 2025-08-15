<?php

namespace App\Entity\Book\ElectronicBook\Information;

use App\Entity\Mediatheque\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[Vich\Uploadable]
#[ORM\Entity]
#[ORM\Table(name: 'electronic_book_information_book')]
class Book extends File
{
    #[Assert\NotNull(groups: ['book_create'])]
    #[Assert\File(mimeTypes: ['application/zip', 'application/epub+zip'])]
    #[Vich\UploadableField(mapping: "electronicBookInformation_book", fileNameProperty: "path")]
    protected ?HttpFile $file;

    #[ORM\OneToOne(targetEntity: \App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation::class, mappedBy: 'bookFile')]
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
