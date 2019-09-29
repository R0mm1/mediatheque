<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\Book\ReferenceGroup;
use App\Entity\Mediatheque\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"book"}, "enable_max_depth"=true},
 *          "denormalization_context"={"groups"={"book"}, "enable_max_depth"=true},
 *          "filters"={"App\Filter\Book\AuthorFullName"}
 *     },
 *     itemOperations={"GET", "PUT", "DELETE"}
 *)
 * @ApiFilter(SearchFilter::class, properties={"title": "partial", "year": "partial", "pageCount": "exact",
 *     "isbn": "partial", "language": "partial", "summary": "partial", "author.firstname": "partial",
 *     "author.lastname": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"title": "ASC", "year": "ASC", "language": "ASC", "pageCount": "ASC", "isbn": "ASC"})
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book", "referenceGroup"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"book", "referenceGroup"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     * @Groups({"book"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"book"})
     */
    private $pageCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"book"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"book"})
     */
    private $language;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"book"})
     */
    private $summary;

    /**
     * @var File|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Mediatheque\File", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @ApiProperty(iri="http://schema.org/cover")
     * @Groups({"book"})
     */
    private $cover = null;


    /**
     * @ORM\OneToOne(targetEntity="ElectronicBook", inversedBy="book", cascade={"persist"})
     * @Groups({"book"})
     * @@MaxDepth(1)
     */
    private $electronicBook;

    /**
     * @ORM\OneToOne(targetEntity="PaperBook", inversedBy="book", cascade={"persist"})
     * @Groups({"book"})
     */
    private $paperBook;

    /**
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="books_authors",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     *     )
     * @Groups({"book"})
     */
    private $authors;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Book\ReferenceGroup", mappedBy="books")
     * @Groups({"book"})
     */
    private $groups;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="books")
     * @Groups({"book"})
     */
    private $owner;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->groups = new ArrayCollection();
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

    public function setPageCount(?int $pageCount): self
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

    public function getSummary()
    {
        return $this->summary;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return ElectronicBook|null
     */
    public function getElectronicBook(): ?ElectronicBook
    {
        return $this->electronicBook;
    }

    public function setElectronicBook($electronicBook): self
    {
        $this->electronicBook = $electronicBook;

        return $this;
    }

    /**
     * @return PaperBook|null
     */
    public function getPaperBook(): ?PaperBook
    {
        return $this->paperBook;
    }

    public function setPaperBook($paperBook): self
    {
        $this->paperBook = $paperBook;

        return $this;
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
     */
    public function getGroups()
    {
        return $this->groups;
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

    public function asArray(array $aBookFields = null, array $aAuthorFields = null, array $aOwnerFields = null): array
    {
        $aReturn = [
            'authors' => []
        ];

        foreach (['Id', 'Title', 'Year', 'Language', 'PageCount', 'Isbn', 'Summary'] as $bookProperty) {
            if (is_null($aBookFields) || in_array($bookProperty, $aBookFields)) {
                $aReturn[lcfirst($bookProperty)] = $this->{"get$bookProperty"}();
            }
        }

        $eBook = $this->getElectronicBook();
        if (is_object($eBook)) {
            $aReturn['ebook'] = $eBook->asArray();
        }

        $authors = $this->getAuthors();
        if ($authors instanceof PersistentCollection) {
            foreach ($authors as $author) {
                $aReturn['authors'][] = $author->asArray($aAuthorFields);
            }
        }

        $paperBook = $this->getPaperBook();
        if (is_object($paperBook)) {
            $aOwner = null;
            $owner = $paperBook->getOwner();
            if (is_object($owner)) {
                $aOwner = $owner->asArray($aOwnerFields);
            }
            $aReturn['owner'] = $aOwner;
        }

        return $aReturn;
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
}
