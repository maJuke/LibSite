<?php

namespace App\Listener;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BookEventListener
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();

        if ($book instanceof Book) {
            $this->updateAuthorBookCount($book);
        }
    }

    private function updateAuthorBookCount(Book $book): void
    {
        foreach ($book->getAuthors() as $author) {
            $author->setAmountOfBooks(count($author->getBooks()) + 1);
        }

        $this->em->flush();
    }
}