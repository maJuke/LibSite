<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;

class BooksController extends AbstractController {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    #[Route('/books', name: 'books', methods: 'GET')]
    public function allBooks(): Response {

        $books = $this
            ->em
            ->getRepository(Book::class)
            ->findAll();

        if (!$books) {
            return new Response(
                content: 'missing books (404)',
                status: 404
            );
        }
        
        $booksArray = [];

        foreach ($books as $book) {
            $booksArray[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'releasedYear' => $book->getReleasedYear(),
                'imagePath' => $book->getImage(),
                'authors' => array_map(fn($author) => [
                    'id' => $author->getFio(),
                    'fio' => $author->getId()
                ], $book->getAuthors()->toArray())
            ];
        }

        return $this->json($booksArray);
    }

    #[Route('/books/{id}', name: 'get_book_by_id', methods: ['GET'])]
    public function exactBook(int $id): Response {

        $book = $this
            ->em
            ->getRepository(Book::class)
            ->find($id);

        if (!$book) {
            return new Response(
                content: "Missing book with id {$id}",
                status: 404);
        }

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'releasedYear' => $book->getReleasedYear(),
            'imagePath' => $book->getImage(),
            'authors' => array_map(fn($author) => [
                'id' => $author->getFio(),
                'fio' => $author->getId()
            ], $book->getAuthors()->toArray())
        ]);
    }

    #[Route('/books/add', name: 'add_book', methods: ['POST'])]
    public function addBook(Request $request, AuthorRepository $authorRepository): Response {
        
        if ($request->getContentType() !== "json") {
            return new Response(
                content: "Wrong content type! JSON needed!",
                status: 400);
        }

        $inputData = json_decode($request->getContent(), true);

        if (!array_key_exists('title', $inputData)
            || !array_key_exists('description', $inputData)
            || !array_key_exists('releasedYear', $inputData)
            || !array_key_exists('imagePath', $inputData)
            || !array_key_exists('authors', $inputData)){
            return new Response(
                content: "Missing right parameters!",
                status: 400);
        }

        if (!isset
            ($inputData['title'], 
                $inputData['releasedYear'], 
                $inputData['description'], 
                $inputData['authors'])) {
            return new Response(
                content: "Missing non-null values!",
                status: 400);
        }

        $book = $this
            ->em
            ->getRepository(Book::class)
            ->saveBook($inputData, $authorRepository);


        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'releasedYear' => $book->getReleasedYear(),
            'imagePath' => $book->getImage(),
            'authors' => array_map(fn($author) => [
                'id' => $author->getId(),
                'fio' => $author->getFio()
            ], $book->getAuthors()->toArray())
        ]);
    }
}
