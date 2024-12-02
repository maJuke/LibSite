<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $title;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'authors')]
    private $authors;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: "integer")]
    private $releasedYear;
    
    #[ORM\Column(type: "string", length: 255)]
    private $imagePath;

    public function __construct() {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(?string $title): self {
        $this->title = $title;
        
        return $this;
    }

    public function getReleasedYear(): ?int {
        return $this->releasedYear;
    }

    public function setReleasedYear(?int $releasedYear): self {
        $this->releasedYear = $releasedYear;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDesctiption(?string $desc): self {
        $this->description = $desc;

        return $this;
    }

    public function getImage(): ?string {
        return $this->imagePath;
    }

    public function setImage(?string $imagePath): self {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection {
        return $this->authors;
    }


    public function addAuthor(Author $author): self {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self {
        $this->authors->removeElement($author);

        return $this;
    }

}