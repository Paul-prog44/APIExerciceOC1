<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'authors', methods:['GET'])]
    public function getAuthors(SerializerInterface $serialiser, AuthorRepository $authorRepository): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $jsonAuthorList = $serialiser->serialize($authorList, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);
    }

    #[Route('api/authors/{id}', name: 'authorDetail', methods:['GET'])]
    public function getAuthorDetail(int $id, AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $author = $authorRepository->find($id);
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true );
    }

    #[Route('api/authors/{id}', name: 'deleteAuthor', methods:['DELETE'])]
    public function deleteAuthor(int $id, AuthorRepository $authorRepository, EntityManagerInterface $em): JsonResponse
    {
        $author = $authorRepository->find($id);
        
        $em->remove($author);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
