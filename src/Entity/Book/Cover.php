<?php


namespace App\Entity\Book;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\Book\CreateCover;
use App\Controller\Book\GetCover;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Mediatheque\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Mediatheque\FileRepository")
 * @package App\Entity\Book
 * @Vich\Uploadable
 */
#[ApiResource(
    types: ['http://schema.org/Book/Cover'],
    operations: [
        new Get(
            controller: GetCover::class
        ),
        new Post(
            defaults: ['_api_receive' => false],
            controller: CreateCover::class,
            openapiContext: [
                'summary' => 'Create a cover to link to a book',
                'description' => 'Create a cover from an uploaded file or from a book uploaded to the electronic book parser',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'oneOf' => [
                                    [
                                        'name' => 'file',
                                        'type' => 'file',
                                        'description' => 'The cover to upload',
                                        'required' => false
                                    ],
                                    [
                                        'name' => 'electronic_book_information_image_id',
                                        'type' => 'string',
                                        'description' => 'Use a book previously uploaded to the electronic book parser by specifying one id of the resulting ElectronicBookInformation Image resources',
                                        'required' => false
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
    routePrefix: '/book',
    normalizationContext: ['groups' => ['file_read']]
)]
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
