<?php

namespace App\Tests\api\book\paperBook;

use App\Entity\Author;
use App\Entity\Book\Cover;
use App\Entity\Editor;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    private Author $author1;
    private Author $author2;
    private User $owner;
    private Editor $editor;
    private Cover $cover;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = static::getContainer();
        /**@var $entityManager EntityManagerInterface */
        $entityManager = $container->get(EntityManagerInterface::class);

        $this->author1 = (new Author())
            ->setLastname("Hugo")
            ->setFirstname("Victor");
        $entityManager->persist($this->author1);
        $this->author2 = (new Author())
            ->setLastname("Zola")
            ->setFirstname("Émile");
        $entityManager->persist($this->author2);

        $this->owner = (new User('123'))
            ->setFirstname("Romain")
            ->setLastname("Quentel");
        $entityManager->persist($this->owner);

        $this->editor = (new Editor())
            ->setName('Le livre de poche');
        $entityManager->persist($this->editor);

        $this->cover = (new Cover())
            ->setPath('/some-path')
            ->setFile(null);
        $entityManager->persist($this->cover);

        $entityManager->flush();

        $this->client->loginUser($this->owner);
    }

    public function testPostBook()
    {
        $this->client->jsonRequest('POST', '/paper_books', [
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
            'editor'=>'/editors/'.$this->editor->getId(),
            'cover'=>'/book/covers/'.$this->cover->getId()
        ]);

        self::assertResponseStatusCodeSame(201);
    }
}
