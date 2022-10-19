<?php

namespace App\Tests\api\book;

class ReferenceGroupTest extends ReferenceGroupTestCase
{
    public function testListReferenceGroups()
    {
        $expected = [
            [
                "id" => $this->referenceGroup1->getId(),
                "comment" => $this->referenceGroup1->getComment(),
            ],
            [
                "id" => $this->referenceGroup2->getId(),
                "comment" => $this->referenceGroup2->getComment()
            ]
        ];

        $this->client->jsonRequest('GET', '/reference_groups');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals($expected, $responseContent);
    }

    public function testGetReferenceGroup()
    {
        $expected = [
            "id" => $this->referenceGroup1->getId(),
            "elements" => [
                [
                    "id" => $this->referenceGroupElement1_1->getId(),
                    "book" => [
                        "id" => $this->book1->getId(),
                        "title" => $this->book1->getTitle()
                    ],
                    "position" => 0
                ],
                [
                    "id" => $this->referenceGroupElement1_2->getId(),
                    "book" => [
                        "id" => $this->book2->getId(),
                        "title" => $this->book2->getTitle(),
                    ],
                    "position" => 1
                ],
                [
                    "id" => $this->referenceGroupElement1_3->getId(),
                    "book" => [
                        "id" => $this->book3->getId(),
                        "title" => $this->book3->getTitle(),
                    ],
                    "position" => 2
                ]
            ],
            "comment" => $this->referenceGroup1->getComment()
        ];

        $this->client->jsonRequest('GET', '/reference_groups/' . $this->referenceGroup1->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals($expected, $responseContent);
    }

    public function testPostReferenceGroup()
    {
        $referenceGroupData = [
            'comment' => 'Test reference group creation',
            'elements' => [
                '/reference_group_books/' . $this->referenceGroupElement1_1->getId()
            ]
        ];

        $this->client->jsonRequest('POST', '/reference_groups', $referenceGroupData);
        self::assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey('id', $responseContent);
        $this->assertMatchesRegularExpression('#\d*#', $responseContent['id']);

        unset($responseContent['id']);
        $expected = [
            "elements" => [], //elements can't be added at creation to ensure positioning
            "comment" => "Test reference group creation"
        ];
        $this->assertEquals($expected, $responseContent);
    }

    public function testPutReferenceGroup()
    {
        $modifiedData = [
            'comment' => $this->referenceGroup2->getComment() . ' modified'
        ];

        $this->client->jsonRequest(
            'PUT',
            '/reference_groups/' . $this->referenceGroup2->getId(),
            $modifiedData
        );
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $modifiedData['id'] = $this->referenceGroup2->getId();
        $actual = [
            'id' => $responseContent['id'] ?? null,
            'comment' => $responseContent['comment'] ?? null
        ];
        $this->assertEquals($modifiedData, $actual);
    }

    public function testDeleteReferenceGroup()
    {
        $this->client->jsonRequest('DELETE', '/reference_groups/' . $this->referenceGroup2->getId());
        self::assertResponseStatusCodeSame(204);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNull($responseContent);
    }

    public function testSortReferenceGroup()
    {
        //Check the elements have the right position for the test to makes sense
        $this->assertEquals(0, $this->referenceGroupElement1_1->getPosition());
        $this->assertEquals(1, $this->referenceGroupElement1_2->getPosition());
        $this->assertEquals(2, $this->referenceGroupElement1_3->getPosition());

        $this->client->jsonRequest(
            'PUT',
            '/reference_groups/' . $this->referenceGroup1->getId() . '/sort',
            [
                'books' => [
                    $this->referenceGroupElement1_1->getId(),
                    $this->referenceGroupElement1_3->getId() //We don't specify the second element, so it should put at last position
                ]
            ]
        );
        self::assertResponseStatusCodeSame(200);

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('elements', $responseContent);

        uasort($responseContent['elements'], function (array $a, array $b) {
            return $a['position'] - $b['position'];
        });

        $ids = array_values(array_map(function (array $element) {
            return $element['id'];
        }, $responseContent['elements']));

        $this->assertEquals(
            [
                $this->referenceGroupElement1_1->getId(),
                $this->referenceGroupElement1_3->getId(),
                $this->referenceGroupElement1_2->getId()
            ],
            $ids
        );
    }
}
