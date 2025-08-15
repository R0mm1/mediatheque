<?php

namespace App\Entity\Book\AudioBook;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\DataProvider\Book\BookFileDataProvider\AudioBookFileDataProvider;
use App\Entity\Mediatheque\File as BaseFile;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ApiResource(
    shortName: 'AudioBookFile',
    types: ['http://schema.org/Book/BookFile'],
    operations: [
        new Get(
            provider: AudioBookFileDataProvider::class
        ),
        new Post(
            defaults: ['_api_receive' => false],
            controller: \App\Controller\Book\AudioBookFile\Post::class,
            openapiContext: [
                'summary' => 'Create an audio book file to link to a book',
                'description' => 'Create an audio book file from an uploaded file',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                [
                                    'type' => 'object',
                                    'properties' => [
                                        [
                                            'name' => 'file',
                                            'type' => 'file',
                                            'description' => 'The file to upload',
                                            'required' => false
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            validationContext: ['groups' => ['Default', 'file_create']]
        )
    ],
    normalizationContext: ['groups' => ['file_read']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'audio_book_file')]
class File extends BaseFile
{
    #[Vich\UploadableField(mapping: "book_audioBook", fileNameProperty: "path")]
    #[Assert\NotNull(groups: ['file_create'])]
    #[Assert\File(mimeTypes: ['application/zip', 'audio/mpeg'])]
    protected ?HttpFile $file;

    #[ORM\OneToOne(targetEntity: \App\Entity\Book\AudioBook\Book::class, mappedBy: 'bookFile')]
    protected ?Book $audioBook = null;

    public function getAudioBook(): ?Book
    {
        return $this->audioBook;
    }

    public function setAudioBook(?Book $audioBook): self
    {
        $this->audioBook = $audioBook;

        return $this;
    }
}
