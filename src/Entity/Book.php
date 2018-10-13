<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\OneToOne(targetEntity="ElectronicBook", inversedBy="book")
     */
    private $electronicBook;

    /**
     * @ORM\OneToOne(targetEntity="PaperBook", inversedBy="book")
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

    public function getElectronicBook()
    {
        return $this->electronicBook;
    }

    public function setElectronicBook($electronicBook): self
    {
        $this->electronicBook = $electronicBook;

        return $this;
    }

    public function getPaperBook()
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
        if ($this->authors instanceof ArrayCollection && !$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function asArray(array $aBookFields = null, array $aAuthorFields = null)
    {
        $aReturn = [
            'authors' => []
        ];

        foreach (['Id', 'Title', 'Year', 'Language'] as $bookProperty) {
            if (is_null($aBookFields) || in_array($bookProperty, $aBookFields)) {
                $aReturn[lcfirst($bookProperty)] = $this->{"get$bookProperty"}();
            }
        }

        $authors = $this->getAuthors();
        if ($authors instanceof PersistentCollection) {
            foreach ($authors as $author) {
                $aReturn['authors'][] = $author->asArray($aAuthorFields);
            }
        }

        return $aReturn;
    }
}
