<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;

class BooksController extends AbstractController {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    #[Route('/books', name: 'books')]
    public function index(): Response {
        
        $books = $this
            ->em
            ->getRepository(Book::class)
            ->createQueryBuilder('b')
            ->leftJoin('b.authors', 'a')
            ->addSelect('a')
            ->getQuery();
        $result = $books->getArrayResult();

        return new JsonResponse($result);
    }
}
