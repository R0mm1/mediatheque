<?php


namespace App\Entity\Book\ElectronicBook;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\DataProvider\Book\BookFileDataProvider\ElectronicBookFileDataProvider;
use App\Entity\Mediatheque\File as BaseFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @ORM\Table(name="book_file")
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
#[ApiResource(
    shortName: 'BookFile',
    types: ['http://schema.org/Book/BookFile'],
    operations: [
        new Get(
            provider: ElectronicBookFileDataProvider::class
        ),
        new Post(
            defaults: ['_api_receive' => false],
            controller: \App\Controller\Book\BookFile\Post::class,
            openapiContext: [
                'summary' => 'Create a book file to link to a book',
                'description' => 'Create a book file from an uploaded file or from a book uploaded to the electronic book parser',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                [
                                    'type' => 'object',
                                    'oneOf' => [
                                        [
                                            'name' => 'file',
                                            'type' => 'file',
                                            'description' => 'The file to upload',
                                            'required' => false
                                        ],
                                        [
                                            'name' => 'electronic_book_information_id',
                                            'type' => 'string',
                                            'description' => 'Use a book previously uploaded to the electronic book parser by specifying the id of the resulting ElectronicBookInformation resource',
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
class File extends BaseFile
{
    /**
     * @var HttpFile|null
     *
     * @Assert\NotNull(groups={"file_create"})
     * @Vich\UploadableField(mapping="book_electronicBook", fileNameProperty="path")
     * @Assert\File(
     *     mimeTypes = {"application/zip", "application/epub+zip"},
     *     )
     */
    protected $file;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Book\ElectronicBook\Book", mappedBy="bookFile")
     */
    protected $electronicBook;

    /**
     * @return mixed
     */
    public function getElectronicBook()
    {
        return $this->electronicBook;
    }

    /**
     * @param $electronicBook
     * @return $this
     */
    public function setElectronicBook($electronicBook): self
    {
        $this->electronicBook = $electronicBook;

        return $this;
    }
}
