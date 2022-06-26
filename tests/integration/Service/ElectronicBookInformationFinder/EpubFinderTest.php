<?php

namespace App\Tests\integration\Service\ElectronicBookInformationFinder;

use App\Entity\Book\ElectronicBook\Information\Book;
use App\Service\ElectronicBookInformationFinder\EpubFinder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EpubFinderTest extends KernelTestCase
{
    private EpubFinder $epubFinder;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->epubFinder = $container->get(EpubFinder::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    /**
     * @dataProvider findDataProvider
     */
    public function testFind(
        string $filePath,
        string $expectedTitle,
        array  $expectedImages
    )
    {
        //The file is deleted at the end of the process, so we create a working copy
        $pathinfo = pathinfo($filePath);
        $copyFilePath = sprintf(
            '%s/%s_workingCopy.%s',
            $pathinfo['dirname'],
            $pathinfo['filename'],
            $pathinfo['extension']
        );
        copy($filePath, $copyFilePath);
        $filePath = $copyFilePath;

        //The file need to be persisted to trigger the VichUploadBundle stuff
        $book = (new Book())
            ->setFile(new UploadedFile($filePath, basename($filePath), null, null, true));
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $electronicBookInformation = $this->epubFinder->find($book);

        $this->assertEquals($expectedTitle, $electronicBookInformation->getTitle());

        $this->assertCount(count($expectedImages), $electronicBookInformation->getImages());
        foreach ($electronicBookInformation->getImages() as $image) {
            $matching = array_filter($expectedImages, function ($expectedImage) use ($image) {
                return $expectedImage['name'] === $image->getName()
                    && $expectedImage['type'] === $image->getType()
                    && preg_match($expectedImage['path'], $image->getPath()) !== false;
            });
            $this->assertCount(1, $matching);
        }
    }

    public function findDataProvider(): array
    {
        return [
            'A valid epub 2.0 file' => [
                'filePath' => __DIR__ . '/../../../files/book/valid/book_epub_20_1.epub',
                'expectedTitle' => 'Epub 2.0 1',
                'expectedImages' => [
                    [
                        'name' => 'cover',
                        'type' => 'COVER',
                        //The path cannot be guessed exactly so instead we provide a regex to match against
                        'path' => '#assets/files/book/electronicBookInformation/book/[a-f0-9]*_extracted/cover\.jpeg#'
                    ]
                ]
            ],
            'A valid epub 3.0 file' => [
                'filePath' => __DIR__ . '/../../../files/book/valid/book_epub_30_1.epub',
                'expectedTitle' => 'Epub 3.0 1',
                'expectedImages' => [
                    [
                        'name' => 'cover-image',
                        'type' => 'COVER',
                        //The path cannot be guessed exactly so instead we provide a regex to match against
                        'path' => '#assets/files/book/electronicBookInformation/book/[a-f0-9]*_extracted/cover\.jpeg#'
                    ]
                ]
            ]
        ];
    }
}
