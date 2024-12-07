<?php

namespace App\Repository;

use App\Entity\Author; 
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AuthorRepository extends ServiceEntityRepository {

    private $em;
    
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager) {
        parent::__construct($registry, Author::class);
        $this->em = $entityManager;
    }

    public function saveAuthor(array $inputData, BookRepository $bookRepository): Author {

        $author = new Author();

        $author->setFio($inputData['fio']);
        $author->setAmountOfBooks($inputData['amountOfBooks']);

        foreach ($inputData['books'] as $bookId) {
            $book = $bookRepository->find($bookId);
            if ($book) {
                $author->addBook($book);
            }
        }

        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }
}