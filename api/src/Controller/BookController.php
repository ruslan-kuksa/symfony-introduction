<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $entityManager,
    )
    {}
    #[Route('/api/book-create', name: 'app_author', methods: ['POST'])]
    public function bookCreate(Request $request): Response
    {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($book, 'json');

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/book-list', name: 'app_author', methods: ['GET'])]
    public function bookList(Request $request): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();
        $jsonData = $this->serializer->serialize($books, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_author', methods: ['GET'])]
    public function book(Request $request, string $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $id]);
        $jsonData = $this->serializer->serialize($book, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_author', methods: ['DELETE'])]
    public function bookDelete(Request $request, string $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
        $jsonData = $this->serializer->serialize($book, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_author', methods: ['PUT'])]
    public function bookUpdate(Request $request, string $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $id]);
        $data = $this->serializer->deserialize($request->getContent(), Book::class, 'json', ['object_to_populate' => $book]);
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $jsonData = $this->serializer->serialize($data, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
}
