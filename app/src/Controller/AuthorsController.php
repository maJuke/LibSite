<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorsController extends AbstractController {
    
    #[Route('/authors', name: 'authors')]
    public function index(): Response {
        return $this->json([
            'message' => 'Hello, AuthorsController!',
            'path' => 'src/Controller/AuthorsController.php'
        ]);
    }
}
