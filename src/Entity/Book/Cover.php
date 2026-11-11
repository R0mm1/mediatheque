<?php


namespace App\Entity\Book;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Post;
use App\Controller\Book\CreateCover;
use App\Controller\Book\GetCover;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Mediatheque\File;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[Vich\Uploadable]
#[ApiResource(
    types: ['http://schema.org/Book/Cover'],
    operations: [
        new Get(
            controller: GetCover::class
        ),
        new Post(
            defaults: ['_api_receive' => false],
            controller: CreateCover::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'oneOf' => [
                                    [
                                        'name' => 'file',
                                        'type' => 'file',
                                        'description' => 'The cover to upload',
                                        'required' => false,
                                        "inputFormats" => ['multipart' => ['multipart/form-data']],
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
                    ])
                )
            ),
            validationContext: ['groups' => ['Default', 'file_create']]
        )
    ],
    routePrefix: '/book',
    normalizationContext: ['groups' => ['file_read']]
)]
#[ORM\Entity(repositoryClass: \App\Repository\Mediatheque\FileRepository::class)]
class Cover extends File
{
    #[Assert\NotNull(groups: ['file_create'])]
    #[Vich\UploadableField(mapping: 'book_cover', fileNameProperty: 'path')]
    protected ?HttpFile $file;
}
