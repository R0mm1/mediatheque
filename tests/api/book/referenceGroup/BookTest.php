<?php

namespace App\Tests\api\book\referenceGroup;

use App\Tests\api\book\ReferenceGroupTestCase;

class BookTest extends ReferenceGroupTestCase
{
    public function testListReferenceGroupBooks()
    {
        $expected = [
            [
                "id" => $this->referenceGroupElement1_1->getId(),
                "book" => [
                    "id" => $this->book1->getId(),
                    "title" => $this->book1->getTitle()
                ],
                "position" => 0,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ],
            [
                "id" => $this->referenceGroupElement1_2->getId(),
                "book" => [
                    "id" => $this->book2->getId(),
                    "title" => $this->book2->getTitle()
                ],
                "position" => 1,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ],
            [
                "id" => $this->referenceGroupElement1_3->getId(),
                "book" => [
                    "id" => $this->book3->getId(),
                    "title" => $this->book3->getTitle()
                ],
                "position" => 2,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ],
            [
                "id" => $this->referenceGroupElement2_1->getId(),
                "book" => [
                    "id" => $this->book2->getId(),
                    "title" => $this->book2->getTitle()
                ],
                "position" => 0,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup2->getId()
            ],
            [
                "id" => $this->referenceGroupElement2_2->getId(),
                "book" => [
                    "id" => $this->book3->getId(),
                    "title" => $this->book3->getTitle()
                ],
                "position" => 1,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup2->getId()
            ]
        ];

        $this->client->jsonRequest('GET', '/reference_group_books');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals($expected, $responseContent);
    }

    public function testListReferenceGroupFilter()
    {
        $expected = [
            [
                "id" => $this->referenceGroupElement1_1->getId(),
                "book" => [
                    "id" => $this->book1->getId(),
                    "title" => $this->book1->getTitle()
                ],
                "position" => 0,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ],
            [
                "id" => $this->referenceGroupElement1_2->getId(),
                "book" => [
                    "id" => $this->book2->getId(),
                    "title" => $this->book2->getTitle()
                ],
                "position" => 1,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ],
            [
                "id" => $this->referenceGroupElement1_3->getId(),
                "book" => [
                    "id" => $this->book3->getId(),
                    "title" => $this->book3->getTitle()
                ],
                "position" => 2,
                "referenceGroup" => "/reference_groups/" . $this->referenceGroup1->getId()
            ]
        ];

        $this->client->jsonRequest('GET', '/reference_group_books?referenceGroup=' . $this->referenceGroup1->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals($expected, $responseContent);
    }

    public function testGetReferenceGroupBook()
    {
        $expected = [
            "id" => $this->referenceGroupElement1_1->getId(),
            "book" => [
                "id" => $this->book1->getId(),
                "title" => $this->book1->getTitle()
            ],
            "position" => $this->referenceGroupElement1_1->getPosition(),
            "referenceGroup" => [
                "id" => $this->referenceGroup1->getId(),
                "comment" => $this->referenceGroup1->getComment()
            ]
        ];

        $this->client->jsonRequest('GET', '/reference_group_books/' . $this->referenceGroupElement1_1->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals($expected, $responseContent);
    }

    public function testPostReferenceGroupBook()
    {
        $referenceGroupBookData = [
            "book" => "/audio_books/" . $this->book3->getId(),
            "position" => 2,
            "referenceGroup" => '/reference_groups/' . $this->referenceGroup1->getId()
        ];

        $this->client->jsonRequest('POST', '/reference_group_books', $referenceGroupBookData);
        self::assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        unset($responseContent['id']);
        $expected = [
            "book" => [
                "id" => $this->book3->getId(),
                "title" => $this->book3->getTitle()
            ],
            "position" => 2,
            "referenceGroup" => [
                "id" => $this->referenceGroup1->getId(),
                "comment" => "Reference group 1"
            ]
        ];
        $this->assertEquals($expected, $responseContent);
    }

    public function testPutReferenceGroupBook()
    {
        $modifiedData = [
            'position' => 4
        ];

        $this->client->jsonRequest('PUT', '/reference_group_books/' . $this->referenceGroupElement1_1->getId(), $modifiedData);
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $expected = [
            "id" => $this->referenceGroupElement1_1->getId(),
            "book" => [
                "id" => $this->book1->getId(),
                "title" => $this->book1->getTitle()
            ],
            "position" => 4,
            "referenceGroup" => [
                "id" => $this->referenceGroup1->getId(),
                "comment" => $this->referenceGroup1->getComment()
            ]
        ];
        $this->assertEquals($expected, $responseContent);
    }

    public function testDeleteReferenceGroupBook()
    {
        $this->client->jsonRequest('DELETE', '/reference_group_books/' . $this->referenceGroupElement1_1->getId());
        self::assertResponseStatusCodeSame(204);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNull($responseContent);
    }
}
