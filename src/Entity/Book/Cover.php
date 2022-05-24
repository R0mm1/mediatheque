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
 * @ORM\Entity(repositoryClass="App\Repository\Mediatheque\FileRepository")
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
    protected $file;
}
