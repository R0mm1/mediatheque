<?php

namespace App\Entity\Book\ElectronicBook\Information;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\DataProvider\ElectronicBookInformation\ImageFileDataProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new Get(
            uriTemplate: '/images/{id}/file',
            provider: ImageFileDataProvider::class
        )
    ],
    routePrefix: '/electronic_book_information',
    normalizationContext: ['groups' => ['electronicBookInformation_image:get']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'electronic_book_information_image')]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['electronicBookInformation:get', 'electronicBookInformation_image:get'])]
    private ?string $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['electronicBookInformation:get', 'electronicBookInformation_image:get'])]
    private ?string $name;

    #[ORM\Column(type: 'string')]
    #[Groups(['electronicBookInformation:get', 'electronicBookInformation_image:get'])]
    private ?string $type;

    #[ORM\Column(type: 'string')]
    private ?string $path;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
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
     * @return Image
     */
    public function setPath(?string $path): Image
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Image
     */
    public function setName(?string $name): Image
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param ImageType $type
     * @return Image
     */
    public function setType(ImageType $type): Image
    {
        $this->type = $type->name;
        return $this;
    }
}
