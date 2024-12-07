<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class BookRepository extends ServiceEntityRepository {
    
    private $em;
    
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager) {
        parent::__construct($registry, Book::class);
        $this->em = $entityManager;
    }

    public function saveBook(array $inputData, AuthorRepository $authorRepository) : Book {
        
        $book = new Book();

        $book->setTitle($inputData['title']);
        $book->setDesctiption($inputData['description']);
        $book->setReleasedYear($inputData['releasedYear']);
        $book->setImage($inputData['imagePath']);

        foreach ($inputData['authors'] as $authorId) {
            $author = $authorRepository->find($authorId);
            if ($author) {
                $book->addAuthor($author);
            }
        }

        $this->em->persist($book);
        $this->em->flush();
        
        return $book;
    }
}