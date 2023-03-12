<?php

namespace App\Entity\Book\ElectronicBook\Information;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\ElectronicBookInformationPost;
use App\DataPersister\ElectronicBookInformation\ElectronicBookInformationDataPersister;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 */
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            defaults: ['_api_receive' => false],
            controller: ElectronicBookInformationPost::class,
            openapiContext: [
                'consumes' => 'multipart/form-data',
                'parameters' => [
                    [
                        'in' => 'formData',
                        'name' => 'electronicBook',
                        'type' => 'file',
                        'description' => 'The electronic book to parse'
                    ]
                ]
            ],
            validationContext: ['groups' => ['book_create']]
        ),
        new Delete(
            processor: ElectronicBookInformationDataPersister::class
        )
    ],
    routePrefix: '/electronic_book_information',
    normalizationContext: ['groups' => ['electronicBookInformation:get']],
    denormalizationContext: ['groups' => ['electronicBookInformation:set']]
)]
class ElectronicBookInformation
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"electronicBookInformation:get"})
     */
    private ?string $id;


    /**
     * @ORM\OneToOne(targetEntity="\App\Entity\Book\ElectronicBook\Information\Book", inversedBy="electronicBookInformation")
     * @Groups({"electronicBookInformation:set"})
     */
    private ?Book $bookFile;

    /**
     * @ORM\ManyToMany(targetEntity="\App\Entity\Book\ElectronicBook\Information\Image", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="electronic_book_information_electronic_book_information_image",
     *      joinColumns={@ORM\JoinColumn(name="electronic_book_information_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="electronic_book_information_image_id", referencedColumnName="id", unique=true, onDelete="CASCADE")}
     * )
     * @Groups({"electronicBookInformation:get"})
     */
    private Collection $images;

    /**
     * @ORM\Column(type="string")
     * @Groups({"electronicBookInformation:get"})
     */
    private ?string $title;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }
        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->contains($image)) {
            $this->images->remove($image->getId());
        }
        return $this;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images->toArray();
    }

    /**
     * @return Book|null
     */
    public function getBookFile(): ?Book
    {
        return $this->bookFile;
    }

    /**
     * @param Book|null $bookFile
     * @return ElectronicBookInformation
     */
    public function setBookFile(?Book $bookFile): ElectronicBookInformation
    {
        $this->bookFile = $bookFile;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return ElectronicBookInformation
     */
    public function setTitle(?string $title): ElectronicBookInformation
    {
        $this->title = $title;
        return $this;
    }
}
