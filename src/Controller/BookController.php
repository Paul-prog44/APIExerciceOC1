<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
    public function deleteBook(int $id, BookRepository $bookRepository, EntityManagerInterface $em) : JsonResponse
    {
        $book = $bookRepository->find($id);

        $em->remove($book);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/books', name: 'createBook', methods:['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository) : JsonResponse
    {
        //La variable $book va contenir la déserialisation (conversion en object) de la requête qui va hydrater un objet book 
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');

        //récuperation de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        //recuperationd de l'idAuthor si il n'existe pas, sa valeur sera null
        $idAuthor = $content['idAuthor'] ?? -1;

        $book->setAuthor($authorRepository->find($idAuthor));
        $em->persist($book);
        $em->flush();

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);

        $location = $urlGenerator->generate('bookDetail', ['id' => $book->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('api/books/{id}', name: 'updateBook', methods:['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $em, Book $currentBook, AuthorRepository $authorRepository) : JsonResponse
    {
        $updatedBook = $serializer->deserialize($request->getContent(), Book::class, 'json',
         [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);
         
         //récuperation de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        //recuperationd de l'idAuthor si il n'existe pas, sa valeur sera null
        $idAuthor = $content['idAuthor'] ?? -1;

        $updatedBook->setAuthor($authorRepository->find($idAuthor));
        $em->persist($updatedBook);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }



    

}
