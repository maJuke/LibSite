<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController {
    
    #[Route('/books', name: 'books')]
    public function index(): Response {
        return $this->json([
            'message' => 'Hello, BookController!',
            'path' => 'src/Controller/BooksController.php'
        ]);
    }
}
