<?php

namespace App\Repository;

use App\Entity\Author;
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

    public function editBook(array $inputData, Book $book): void {
        
        if (isset($inputData['title'])) {
            $book->setTitle($inputData['title']);
        }

        if (isset($inputData['releasedYear'])) {
            $book->setReleasedYear($inputData['releasedYear']);
        }

        if (isset($inputData['description'])) {
            $book->setDesctiption($inputData['description']);
        }

        if (isset($inputData['imagePath'])) {
            $book->setImage($inputData['imagePath']);
        }

        if (isset($inputData['authors']) && is_array($inputData['authors'])) {
            
            $authors = $this
                ->em
                ->getRepository(Author::class)
                ->findBy([
                    'id' => $inputData['authors']
                ]);
            
            foreach ($book->getAuthors() as $authorExist) {
                    $book->removeAuthor($authorExist);
            }

            foreach ($authors as $author) {
                $book->addAuthor($author);
            }

        }
        $this->em->flush();
    }

    public function findBookWithFilters($authorCount, $yearFilter) : array {

        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.authors', 'b')
            ->groupBy('a.id');

        if ($authorCount !== null) {
            $queryBuilder->having('COUNT(b.id) = :authorCount')
                ->setParameter('authorCount', $authorCount);
            }

        if ($yearFilter !== null) {
            $queryBuilder->andWhere('a.releasedYear = :yearFilter')
                ->setParameter('yearFilter', $yearFilter);
        }
        return $queryBuilder->getQuery()->getResult();
    }
}