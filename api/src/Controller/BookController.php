<?php

namespace App\Controller;

use App\Entity\Author;
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
    #[Route('/api/book-create', name: 'app_book', methods: ['POST'])]
    public function bookCreate(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');
        $author = $this->entityManager->getRepository(Author::class)->find($data['author_id']);

        $book->setAuthor($author);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($book, 'json');

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/book-list', name: 'app_book_get', methods: ['GET'])]
    public function bookList(Request $request): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findAll();

        return $this->render('book/list.html.twig', [
            'books' => $books,
        ]);
    }
    #[Route('/api/book/{id}', name: 'app_book_get_id', methods: ['GET'])]
    public function book(Request $request, string $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $id]);
        $jsonData = $this->serializer->serialize($book, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_book_delete', methods: ['DELETE'])]
    public function bookDelete(Request $request, string $id): Response
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($book);
        $this->entityManager->flush();
        $jsonData = $this->serializer->serialize($book, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/book/{id}', name: 'app_book_update', methods: ['PUT'])]
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
