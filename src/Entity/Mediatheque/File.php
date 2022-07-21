<?php

namespace App\Entity\Mediatheque;

use ApiPlatform\Core\Annotation\ApiProperty;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\Mediatheque\CreateFile;
use Symfony\Component\HttpFoundation\File\File as HttpFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Mediatheque\FileRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "electronicBookInformation_image" = "\App\Entity\Book\ElectronicBook\Information\Image",
 *     "cover" = "\App\Entity\Book\Cover",
 *     "bookfile" = "\App\Entity\Book\ElectronicBook\File",
 *     "audiobook_file" = "\App\Entity\Book\AudioBook\File",
 *     "electronicBookInformation_book" = "\App\Entity\Book\ElectronicBook\Information\Book",
 * })
 * @ORM\HasLifecycleCallbacks
 * @ApiResource(
 *     iri="http://schema.org/File",
 *     normalizationContext={
 *          "groups"={"file_read"}
 *     },
 *     collectionOperations={
 *          "post": {
 *              "controller"=CreateFile::class,
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
 *                         "description"="The file to upload",
 *                     },
 *                 },
 *             }
 *          }
 *     },
 *     itemOperations={
 *          "get"
 *     }
 * )
 * @Vich\Uploadable
 */
class File implements FileInterface
{
    const STATUS_TEMP = 1;
    const STATUS_VALID = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"file_read"})
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"file_read"})
     */
    protected ?string $path;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"file_read"})
     */
    protected ?int $status;

    /**
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="file", fileNameProperty="path")
     */
    protected ?HttpFile $file;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?DateTime $updatedAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return HttpFile|null
     */
    public function getFile(): ?HttpFile
    {
        return $this->file;
    }

    /**
     * @throws Exception
     */
    public function setFile(?HttpFile $file): self
    {
        $this->file = $file;
        $this->updatedAt = new DateTime('now');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (empty($this->status)) {
            $this->setStatus(self::STATUS_TEMP);
        }
    }
}
