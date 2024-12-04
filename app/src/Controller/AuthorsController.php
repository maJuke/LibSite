<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorsController extends AbstractController {
    
    #[Route('/authors', name: 'authors')]
    public function index(EntityManagerInterface $em): Response {
        
        $repository = $em->getRepository(Author::class);
        $authors = $repository->findAll();

        dd($authors);

        return $this->render('index.html.twig');
    }
}
