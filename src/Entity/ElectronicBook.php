<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\ElectronicBook\Post;
use App\Controller\ElectronicBook\Get;
use App\Controller\ElectronicBook\Delete;
use App\Controller\Book\GetElectronicBookRawData;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Mediatheque\FileRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class ElectronicBook extends \App\Entity\Mediatheque\File
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="book_electronicBook", fileNameProperty="path")
     */
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="Book", mappedBy="electronicBook")
     */
    private $book;


    public function getBook()
    {
        return $this->book;
    }

    public function setBook($book): self
    {
        $this->book = $book;

        return $this;
    }
}
