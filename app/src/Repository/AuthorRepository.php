<?php

namespace App\Repository;

use App\Entity\Book;
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
        $author->setAmountOfBooks(0);

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

    public function editAuthor(array $inputData, Author $author): void {

        if (isset($inputData['fio'])) {
            $author->setFio($inputData['fio']);
        } 

        if (isset($inputData['books']) && is_array($inputData['books'])) {
            
            $books = $this
                ->em
                ->getRepository(Book::class)
                ->findBy([
                    'id' => $inputData['books']
                ]);

            foreach ($author->getBooks() as $bookExists) {
                $author->removeBook($bookExists);
            }

            foreach ($books as $book) {
                $author->addBook($book);
            }
        }
        $this->em->flush();
    }

    public function findAuthorsWithFilters($bookCounter) : array {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($bookCounter !== null) {
            $queryBuilder->where('a.amountOfBooks = :bookCounter')
            ->setParameter('bookCounter', $bookCounter);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}