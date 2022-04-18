<?php


namespace App\Entity\Book;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Mediatheque\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Book\CreateCover;
use App\Controller\Book\GetCover;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
class BookFile extends File
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="book_electronicBook", fileNameProperty="path")
     * @Assert\File(
     *     mimeTypes = {"application/zip", "application/epub+zip"},
     *     )
     */
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ElectronicBook", mappedBy="bookFile")
     */
    protected $electronicBook;

    /**
     * @return mixed
     */
    public function getElectronicBook()
    {
        return $this->electronicBook;
    }

    /**
     * @param $electronicBook
     * @return $this
     */
    public function setElectronicBook($electronicBook): self
    {
        $this->electronicBook = $electronicBook;

        return $this;
    }
}
