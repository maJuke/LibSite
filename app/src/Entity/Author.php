<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass:AuthorRepository::class)]
class Author{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $fio;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private $books;

    #[ORM\Column(type: "integer", nullable: false, options: ["default" => 0])]
    private $amountOfBooks;

    public function __construct() {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFio(): ?string {
        return $this->fio;
    }

    public function setFio(?string $fio): self {
        $this->fio = $fio;
        
        return $this;
    }

    public function getAmountOfBooks(): ?int {
        return $this->amountOfBooks;
    }

    public function setAmountOfBooks(?int $amountOfBooks): self {
        $this->amountOfBooks = $amountOfBooks;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection {
        return $this->books;
    }


    public function addBook(Book $book): self {

        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addAuthor($this);
        }
        return $this;;
    }

    public function removeBook(Book $book): self {
        
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            $book->removeAuthor($this);
        }
        return $this;
    }
}