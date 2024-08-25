<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $entityManager,
    )
    {}
    #[Route('/api/author-create', name: 'app_author', methods: ['POST'])]
    public function create_author(Request $request): JsonResponse
    {
        $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');


        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($author, 'json');

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/author-list', name: 'app_author_list', methods: ['GET'])]
    public function list_authors(Request $request, string $id): JsonResponse
    {
        $data = $this->entityManager->getRepository(Author::class)->findAll();

        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);

    }

    #[Route('/api/author/{id}', name: 'app_author', methods: ['GET'])]
    public function get_author(Request $request, string $id): JsonResponse
    {
        $data = $this->entityManager->getRepository(Author::class)->findOneBy(['id' => $id]);

        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}/edit', name: 'app_author_edit', methods: ['PUT'])]
    public function edit_author(Request $request, string $id): JsonResponse
    {
        $author = $this->entityManager->getRepository(Author::class)->findOneBy(['id' => $id]);
        $data = $this->serializer->deserialize($request->getContent(), Author::class, 'json', ['object_to_populate' => $author]);
        $this->entityManager->persist($author);
        $this->entityManager->flush();
        $jsonData = $this->serializer->serialize($data, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}/delete', name: 'app_author_delete', methods: ['DELETE'])]
    public function delete_author(Request $request, string $id): JsonResponse
    {
        $author = $this->entityManager->getRepository(Author::class)->findOneBy(['id' => $id]);
        $this->entityManager->remove($author);
        $this->entityManager->flush();
        return new JsonResponse("Author Deleted", Response::HTTP_OK, [], true);
    }
}
