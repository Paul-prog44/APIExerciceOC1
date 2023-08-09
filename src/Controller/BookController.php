<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractController
{
    #[Route('api/books', name: 'book', methods:['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $booklist = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($booklist, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true );
    }

    #[Route('api/books/{id}', name: 'bookDetail', methods:['GET'])]
    public function getBookDetail(int $id, BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $book = $bookRepository->find($id);
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        return new JsonResponse($jsonBook, Response::HTTP_OK, [], true );
    }

    #[Route('api/books/{id}', name: 'deleteBook', methods:['DELETE'])]
    public function createBook(int $id, BookRepository $bookRepository, EntityManagerInterface $em) : JsonResponse
    {
        $book = $bookRepository->find($id);

        $em->remove($book);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    

}
