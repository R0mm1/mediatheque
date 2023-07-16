<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Filter\Author\Fullname;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['groups' => ['author:list', 'person:list' ]],
            filters: [
                Fullname::class,
                \App\Filter\Author\Person::class,
                'author.search_filter',
                'author.order_filter'
            ]
        ),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['author:get', 'person:get']],
    denormalizationContext: ['groups' => [ 'author:set', 'person:set']]
)]
class Author extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"author:get", "author:list", "book:get", "book:list", "meilisearch"})
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Person", cascade={"persist"})
     * @Groups({"author:get", "author:list", "author:set", "book:get", "book:list", "meilisearch"})
     */
    private ?Person $person;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $bearthYear;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $deathYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $biography;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", mappedBy="authors", cascade={"persist"})
     * @ORM\JoinTable(name="books_authors",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     *     )
     * @Groups({"author:get"})
     */
    private $books;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): Author
    {
        $this->person = $person;
        return $this;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function getFirstname(): ?string
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        return $this->firstname;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function setFirstname(?string $firstname): self
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function getLastname(): ?string
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        return $this->lastname;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function setLastname(string $lastname): self
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function getBearthYear(): ?string
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        return $this->bearthYear;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function setBearthYear(?string $bearthYear): self
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use setBirthYear on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        $this->bearthYear = $bearthYear;

        return $this;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function getDeathYear(): ?string
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        return $this->deathYear;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function setDeathYear(?string $deathYear): self
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        $this->deathYear = $deathYear;

        return $this;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function getBiography(): ?string
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        return $this->biography;
    }

    /**
     * @deprecated Use the same property on the Person entity instead
     */
    public function setBiography(?string $biography): self
    {
        @trigger_error(sprintf('Using %s on %s is deprecated, use the same method on %s instead', __FUNCTION__, __CLASS__, Person::class), \E_USER_DEPRECATED);

        $this->biography = $biography;

        return $this;
    }

    public function getBooks()
    {
        return $this->books;
    }

    public function setBooks($books)
    {
        $this->books = $books;
    }
}
