<?php

namespace App\Tests\api\book;

use App\Entity\Author;
use App\Entity\Book\Cover;
use App\Entity\Editor;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BookTestCase extends WebTestCase
{
    protected Author $author1;
    protected Author $author2;
    protected User $owner;
    protected Editor $editor;
    protected Cover $cover;

    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = static::getContainer();
        /**@var $entityManager EntityManagerInterface */
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->author1 = (new Author())
            ->setLastname("Hugo")
            ->setFirstname("Victor");
        $this->entityManager->persist($this->author1);
        $this->author2 = (new Author())
            ->setLastname("Zola")
            ->setFirstname("Émile");
        $this->entityManager->persist($this->author2);

        $this->owner = (new User('123'))
            ->setFirstname("Romain")
            ->setLastname("Quentel");
        $this->entityManager->persist($this->owner);

        $this->editor = (new Editor())
            ->setName('Le livre de poche');
        $this->entityManager->persist($this->editor);

        $this->cover = (new Cover())
            ->setPath('/some-path')
            ->setFile(null);
        $this->entityManager->persist($this->cover);

        $this->client->loginUser($this->owner);
    }

    protected function assertBookDataIsValid(
        array  $actualBookData,
        string $expectedTitle,
        string $expectedYear,
        int    $expectedPageCount,
        string $expectedIsbn,
        string $expectedLanguage,
        string $expectedSummary,
        array  $expectedAuthors,
        User   $expectedOwner,
        Editor $expectedEditor,
        Cover  $expectedCover
    )
    {
        $this->assertArrayHasKey('title', $actualBookData);
        $this->assertEquals($expectedTitle, $actualBookData['title']);

        $this->assertArrayHasKey('year', $actualBookData);
        $this->assertEquals($expectedYear, $actualBookData['year']);

        $this->assertArrayHasKey('pageCount', $actualBookData);
        $this->assertEquals($expectedPageCount, $actualBookData['pageCount']);

        $this->assertArrayHasKey('isbn', $actualBookData);
        $this->assertEquals($expectedIsbn, $actualBookData['isbn']);

        $this->assertArrayHasKey('language', $actualBookData);
        $this->assertEquals($expectedLanguage, $actualBookData['language']);

        $this->assertArrayHasKey('summary', $actualBookData);
        $this->assertEquals($expectedSummary, $actualBookData['summary']);

        $this->assertArrayHasKey('authors', $actualBookData);
        $authorsData = array_map(function (Author $author) {
            return [
                'id' => $author->getId(),
                'firstname' => $author->getFirstname(),
                'lastname' => $author->getLastname()
            ];
        }, $expectedAuthors);
        $this->assertEquals($authorsData, $actualBookData['authors']);

        $this->assertArrayHasKey('owner', $actualBookData);
        $this->assertEquals('/users/' . $expectedOwner->getId(), $actualBookData['owner']);

        $this->assertArrayHasKey('editor', $actualBookData);
        $this->assertEquals(
            [
                'id' => $expectedEditor->getId(),
                'name' => $expectedEditor->getName()
            ],
            $actualBookData['editor']
        );

        $this->assertArrayHasKey('cover', $actualBookData);
        $this->assertEquals(
            [
                'id' => $expectedCover->getId(),
                'path' => $expectedCover->getPath(),
                'status' => $expectedCover->getStatus()
            ],
            $actualBookData['cover']
        );
    }
}
