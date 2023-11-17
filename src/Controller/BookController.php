<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function initial() : Response {
        return new Response("<h1> welcome to books api </h1>");
    }

    #[Route('/api/books', name: 'books_list', methods: ['GET'])]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        try {
            return $this->json([
                'data' => $bookRepository->findAll(),
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
       
    }

    #[Route('/api/books', name: 'books_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = $request->request->all();

            $book = new Book();
            $book->setTitle($data['title']);
            $book->setIsbn($data['isbn']);
            $book->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
            $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

            $em->persist($book);
            $em->flush();

            return $this->json([
                'message' => 'Book created successfully'
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    #[Route('/api/updateBook/{id}', methods: ['PUT'])]
    public function update($id, Request $request, EntityManagerInterface $em, BookRepository $bookRepository) : JsonResponse {
        try {
            $data = $request->request->all();
            $book = $bookRepository->find($id);

            if(!$book){
                return $this->json([
                    'return' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $book->setTitle($data['title']);
            $book->setIsbn($data['isbn']);
            $book->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

            $em->flush();

            return $this->json([
                'message' => 'Book updated successfully'
            ], 201);

        } catch (\Exception $e) {
           return $this->json([
            'message' => $e->getMessage()
           ], $e->getCode());
        }
    }

    #[Route('/api/remove/{id}', methods: ['DELETE'])]
    public function remove($id, Request $request, EntityManagerInterface $em, BookRepository $bookRepository) : JsonResponse{
        try {
            $book = $bookRepository->find($id);

            if(!$book){
                return $this->json([
                    'return' => false,
                    'message' => 'Book not found'
                ], 404);
            }

            $em->remove($book);
            $em->flush();

            return $this->json([
                'message' => 'Book deleting successfully'
            ], 200);

        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
