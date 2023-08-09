<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('api/authors', name: 'createAuthor', methods:['POST'])]
    public function createAuthor(Request $request, SerializerInterface $serializer,
    UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em) : JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');

        $em->persist($author);
        $em->flush();

        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors']);
        
        $location = $urlGenerator->generate('authorDetail', ['id' => $author->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ['Location' => $location], true);
    } 

    #[Route('api/authors/{id}', name: 'updateAuthor', methods:['PUT'])]
    public function updateAuthor(Request $request, SerializerInterface $serializer, 
    EntityManagerInterface $em, Author $currentAuthor) : JsonResponse
    {
        $updatedAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json',
         [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]);

        $em->persist($updatedAuthor);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }

}
