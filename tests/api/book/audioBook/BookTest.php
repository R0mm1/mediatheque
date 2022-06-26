<?php

namespace App\Tests\api\book\audioBook;

use App\Entity\Book\AudioBook\File;
use App\Tests\api\book\BookTestCase;

class BookTest extends BookTestCase
{
    private File $audioBookFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->audioBookFile = (new File())
            ->setPath('/some-path')
            ->setFile(null);
        $this->entityManager->persist($this->audioBookFile);

        $this->entityManager->flush();
    }

    public function testPostBook()
    {
        $bookData = [
            'title' => 'My audio book',
            'year' => "2022",
            'pageCount' => 158,
            'isbn' => '978-2-7578-8997-8',
            'language' => 'Français',
            'summary' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sit amet ipsum et enim sodales ultrices auctor nec ipsum. Cras urna ante, fermentum nec placerat eget, aliquet ac augue. Etiam ac porta nibh. Aenean a velit vitae leo luctus semper laoreet sed felis. Morbi cursus ac leo a bibendum. Maecenas viverra ipsum fringilla massa molestie, id efficitur orci fringilla. Curabitur congue tincidunt purus, vehicula sollicitudin nulla faucibus id.',
            'authors' => [
                '/authors/' . $this->author1->getId(),
                '/authors/' . $this->author2->getId(),
            ],
            'owner' => '/users/' . $this->owner->getId(),
            'editor' => '/editors/' . $this->editor->getId(),
            'cover' => '/book/covers/' . $this->cover->getId(),
            'bookFile' => '/audio_book_files/' . $this->audioBookFile->getId()
        ];

        $this->client->jsonRequest('POST', '/audio_books', $bookData);

        $this->assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertBookDataIsValid(
            $responseContent,
            $bookData['title'],
            $bookData['year'],
            $bookData['pageCount'],
            $bookData['isbn'],
            $bookData['language'],
            $bookData['summary'],
            [
                $this->author1,
                $this->author2
            ],
            $this->owner,
            $this->editor,
            $this->cover
        );

        $this->assertArrayHasKey('bookFile', $responseContent);
        $this->assertEquals(
            [
                'id' => $this->audioBookFile->getId(),
                'path' => $this->audioBookFile->getPath(),
                'status' => $this->audioBookFile->getStatus()
            ],
            $responseContent['bookFile']
        );
    }
}
