<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Mediatheque\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "electronicbook" = "\App\Entity\Book\ElectronicBook\Book",
 *     "paperbook" = "\App\Entity\Book\PaperBook\Book",
 *     "audiobook" = "\App\Entity\Book\AudioBook\Book"
 * })
 */
class Book extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book:get", "book:list", "book:set", "referenceGroup:get", "referenceGroupBook:get", "referenceGroupBook:list", "author:get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"book:get", "book:list", "book:set", "referenceGroup:get", "referenceGroupBook:get", "referenceGroupBook:list", "author:get"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     * @Groups({"book:get", "book:list", "book:set"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"book:get", "book:set"})
     */
    private $pageCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"book:get", "book:set"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"book:get", "book:list", "book:set"})
     */
    private $language;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"book:get", "book:set"})
     */
    private ?string $summary = null;

    /**
     * @var File|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Book\Cover", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/cover")
     * @Groups({"book:get", "book:set"})
     */
    private $cover = null;

    /**
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="books")
     * @ORM\JoinTable(name="books_authors",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     *     )
     * @Groups({"book:get", "book:list", "book:set"})
     */
    private $authors;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Book\ReferenceGroup", mappedBy="books")
     * @deprecated Books are now related to reference groups through \App\Entity\Book\ReferenceGroup\Book elements
     */
    private $groups;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book\ReferenceGroup\Book", mappedBy="book")
     * @Groups({"book:get"})
     * @var Collection
     */
    private Collection $groupMemberships;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="books")
     * @Groups({"book:get", "book:set"})
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Editor")
     * @Groups({"book:get", "book:set"})
     */
    private ?Editor $editor;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->groupMemberships = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount($pageCount): self
    {
        $this->pageCount = (integer)$pageCount;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary)
    {
        $this->summary = $summary;
    }

    public function getAuthors()
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        $isAC = ($this->authors instanceof ArrayCollection);
        $isPC = ($this->authors instanceof PersistentCollection);

        if (($isAC || $isPC) && !$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $isAC = ($this->authors instanceof ArrayCollection);
        $isPC = ($this->authors instanceof PersistentCollection);

        if (($isAC || $isPC) && $this->authors->contains($author)) {
            $this->authors->removeElement($author);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     * @deprecated
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return Collection
     */
    public function getGroupMemberships(): Collection
    {
        return $this->groupMemberships;
    }

    /**
     * @return File|null
     */
    public function getCover(): ?File
    {
        return $this->cover;
    }

    /**
     * @param $cover
     * @return self
     */
    public function setCover(?File $cover): Book
    {
        $this->cover = $cover;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Editor|null
     */
    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    /**
     * @param Editor|null $editor
     * @return Book
     */
    public function setEditor(?Editor $editor): Book
    {
        $this->editor = $editor;
        return $this;
    }

    /**
     * @Groups({"book:list"})
     */
    public function getShortSummary():string
    {
        if(!is_string($this->getSummary())){
            return '';
        }

        $summary = strip_tags($this->getSummary());
        $summary = html_entity_decode($summary, ENT_QUOTES);
        return mb_substr($summary, 0, 240);
    }
}
