<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AuthorsController extends AbstractController {
    
    private $authorRepository;
    private $bookRepository;

    public function __construct(AuthorRepository $authorRepository, BookRepository $bookRepository) {
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
    }
    
    #[Route('/authors', name: 'authors', methods: 'GET')]
    public function allAuthors(Request $request): Response {

        $bookCounter = $request->query->get('bookCounter');

        $authors = $this
            ->authorRepository
            ->findAuthorsWithFilters($bookCounter);

        if (!$authors) {
            return new Response(
                content: 'missing authors (404)',
                status: 404
            );
        }

        $authorsArray = [];

        foreach ($authors as $author) {
            $authorsArray[] = [
                'id' => $author->getId(),
                'fio' => $author->getFio(),
                'amountOfBooks' => $author->getAmountOfBooks(),
                'books' => array_map(fn($book) => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'releasedYear' => $book->getReleasedYear()
                ], $author->getBooks()->toArray())
            ];
        }

        return $this->json($authorsArray);
    }

    #[Route('authors/{id}', name: 'get_author_by_id', methods: ['GET'])]
    public function exactAuthor(int $id): Response {

        $author = $this
            ->authorRepository
            ->find($id);

        if (!$author) {
            return new Response(
                content: "Missing author with id {$id}",
                status: 404
            );
        }

        return $this->json([
            'id' => $author->getId(),
                'fio' => $author->getFio(),
                'amountOfBooks' => $author->getAmountOfBooks(),
                'books' => array_map(fn($book) => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'releasedYear' => $book->getReleasedYear()
                ], $author->getBooks()->toArray())
            ]);
    }

    #[Route('/authors/add', name: 'add_author', methods: ['POST'])]
    public function addAuthor(Request $request, BookRepository $bookRepository): Response {

        if ($request->getContentType() !== "json") {
            return new Response(
                content: "Wrong content type! JSON needed!",
                status: 400);
        }

        $inputData = json_decode($request->getContent(), true);

        if (!array_key_exists('fio', $inputData)
            || !array_key_exists('books', $inputData)){
            return new Response(
                content: "Missing right parameters!",
                status: 400);
        }

        if (!isset
            ($inputData['fio'], 
                $inputData['books'])) {
            return new Response(
                content: "Missing non-null values!",
                status: 400);
        }

        $author = $this
            ->authorRepository
            ->saveAuthor($inputData, $bookRepository);

        return $this->json([
            'id' => $author->getId(),
            'fio' => $author->getFio(),
            'amountOfBooks' => $author->getAmountOfBooks(),
            'books' => array_map(fn($book) => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'releasedYear' => $book->getReleasedYear()
            ], $author->getBooks()->toArray())
        ]);
    }

    #[Route('authors/edit/{id}', name: 'edit_author', methods: ['POST'])]
    public function editAuthor(int $id, Request $request, AuthorRepository $authorRepository) {

        $author = $authorRepository->find($id);

        if ($request->getContentType() !== "json") {
            return new Response(
                content: "Wrong content type! JSON needed!",
                status: 400);
        } else if (!$author) {
            return new Response(
                content: "Missing author with id {$id}",
                status: 404);
        }

        $inputData = json_decode($request->getContent(), true);

        $authorRepository->editAuthor($inputData, $author);

        return $this->json([
            'message' => "Author with id {$id} has been changed!",
            'author' => [
                'id' => $author->getId(),
                'fio' => $author->getFio(),
                'amountOfBooks' => $author->getAmountOfBooks(),
                'books' => array_map(fn($book) => [
                    'id' => $book->getId(),
                    'title' => $book->getTitle(),
                    'releasedYear' => $book->getReleasedYear()
                ], $author->getBooks()->toArray())
            ]
        ]);
    }

    #[Route('authors/remove/{id}', name: 'remove_author', methods: ['DELETE'])]
    public function removeAuthor(int $id, EntityManagerInterface $em ): Response {
        $author = $this
            ->authorRepository
            ->find($id);

        if (!$author) {
            return new Response(
                content: "Missing author with id {$id}",
                status: 404);
        }

        $em->remove($author);
        $em->flush();

        return new Response(
            content: "Author with id {$id} has been deleted!",
            status: 200
        );
    }
}
