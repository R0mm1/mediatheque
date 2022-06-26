<?php

namespace App\Tests\api\book\paperBook;

use App\Tests\api\book\BookTestCase;

class BookTest extends BookTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager->flush();
    }

    public function testPostBook()
    {
        $bookData = [
            'title' => 'My book',
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
            'cover' => '/book/covers/' . $this->cover->getId()
        ];

        $this->client->jsonRequest('POST', '/paper_books', $bookData);

        self::assertResponseStatusCodeSame(201);
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
    }
}
