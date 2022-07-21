<?php


namespace App\Entity\Book\ElectronicBook;

use App\Entity\Mediatheque\File as BaseFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Book\ElectronicBook\FileRepository")
 * @ORM\Table(name="book_file")
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
class File extends BaseFile
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
    protected ?HttpFile $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBook\Book", mappedBy="bookFile")
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
