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
 * @ApiResource(
 *     iri="http://schema.org/Book/Cover",
 *     routePrefix="/book",
 *     normalizationContext={
 *          "groups"={"file_read"}
 *     },
 *     collectionOperations={
 *          "post": {
 *              "controller"=CreateCover::class,
 *              "defaults"={
 *                 "_api_receive"=false
 *             },
 *             "validation_groups"={"Default", "file_create"},
 *             "swagger_context"={
 *                 "consumes"={
 *                     "multipart/form-data",
 *                 },
 *                 "parameters"={
 *                     {
 *                         "in"="formData",
 *                         "name"="file",
 *                         "type"="file",
 *                         "description"="The cover to upload",
 *                     },
 *                 },
 *             }
 *          }
 *     },
 *     itemOperations={
 *          "get": {
 *              "controller"=GetCover::class
 *          },
 *     }
 * )
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