<?php

namespace App\Entity\Book\AudioBook;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DataProvider\Book\BookRawFileDataProvider;
use App\Entity\Book as BaseBook;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ApiResource(
    shortName: 'AudioBook',
    operations: [
        new Get(
            normalizationContext: ['groups' => ['audioBook:get', 'book:get', 'file_read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['book:list']]
        ),
        new Put(
            normalizationContext: ['groups' => ['audioBook:get', 'book:get', 'file_read']]
        ),
        new Get(
            uriTemplate: '/audio_books/{id}/rawFile',
            normalizationContext: ['groups' => ['file_read']],
            provider: BookRawFileDataProvider::class
        ),
        new Post()
    ],
    normalizationContext: ['groups' => ['book:get', 'file_read']],
    denormalizationContext: ['groups' => ['book:set']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'audio_book')]
class Book extends BaseBook
{
    /**
     * @var File|null
     */
    #[ORM\OneToOne(targetEntity: \App\Entity\Book\AudioBook\File::class, cascade: ['remove', 'persist'], inversedBy: 'audioBook')]
    #[Groups(['book:get', 'book:set'])]
    private ?File $bookFile;

    /**
     * @return File|null
     */
    public function getBookFile(): ?File
    {
        return $this->bookFile;
    }

    /**
     * @param File|null $bookFile
     * @return $this
     */
    public function setBookFile(?File $bookFile): self
    {
        $this->bookFile = $bookFile;

        return $this;
    }
}
