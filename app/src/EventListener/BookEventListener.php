<?php

namespace App\EventListener;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class BookEventListener {
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    private function updateAuthorBookCount(Book $book): void
    {
        
        foreach ($book->getAuthors() as $author) {
            $author = $this->em->getRepository(Author::class)->find($author->getId());
    
            if ($author) {
                $bookCount = $author->getBooks()->count();
                $author->setAmountOfBooks($bookCount);
                $this->em->persist($author);
            }
        }
    
        $this->em->flush();
    }
}