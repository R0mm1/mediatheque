<?php

namespace App\Entity\Mediatheque;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Mediatheque\CreateFile;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
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
 * @Vich\Uploadable
 */
#[ApiResource(
    operations: [
        new Get(),
        new Post(
            defaults: [
                '_api_receive'=> false
            ],
            controller: CreateFile::class,
            openapiContext: [
                'consumes'=> ['multipart/form-data'],
                'parameters' => [
                    [
                        'in'=>'formData',
                        'name'=>'file',
                        'type'=>'file',
                        'description'=>'The file to upload'
                    ]
                ]
            ],
            validationContext: ['groups' => ['Default', 'file_create']]
        )
    ],
    normalizationContext: ['groups' => ['file_read']]
)]
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
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"file_read"})
     */
    protected $path;

    /**
     * @var integer|null
     *
     * @ORM\Column(type="integer")
     * @Groups({"file_read"})
     */
    protected $status;

    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="file", fileNameProperty="path")
     */
    protected $file;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime|null
     */
    protected $updatedAt;

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
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (empty($this->status)) {
            $this->setStatus(self::STATUS_TEMP);
        }
    }
}
