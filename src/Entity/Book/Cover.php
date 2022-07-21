<?php


namespace App\Entity\Book;

use App\Entity\Book;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Mediatheque\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\Book\CreateCover;
use App\Controller\Book\GetCover;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Book\CoverRepository")
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
class Cover extends File
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="book_cover", fileNameProperty="path")
     */
    protected ?HttpFile $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book", mappedBy="cover")
     */
    protected ?Book $book;

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): Cover
    {
        $this->book = $book;
        return $this;
    }
}
