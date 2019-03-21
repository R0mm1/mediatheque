<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pageCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $language;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $picture;


    /**
     * @ORM\OneToOne(targetEntity="ElectronicBook", inversedBy="book", cascade={"persist"})
     */
    private $electronicBook;

    /**
     * @ORM\OneToOne(targetEntity="PaperBook", inversedBy="book", cascade={"persist"})
     */
    private $paperBook;

    /**
     * @ORM\ManyToMany(targetEntity="Author")
     * @ORM\JoinTable(name="books_authors",
     *     joinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     *     )
     */
    private $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
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
        $this->pageCount = $pageCount;

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

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
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

    public function asArray(array $aBookFields = null, array $aAuthorFields = null, array $aOwnerFields = null): array
    {
        $aReturn = [
            'authors' => []
        ];

        foreach (['Id', 'Title', 'Year', 'Language', 'PageCount', 'Isbn', 'Summary', 'Picture'] as $bookProperty) {
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
}
