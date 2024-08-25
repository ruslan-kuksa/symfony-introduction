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
        protected UserPasswordHasherInterface $userPasswordHasher,
    )
    {}
    #[Route('/api/author-create', name: 'app_author', methods: ['POST'])]
    public function create_author(Request $request): JsonResponse
    {
        $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');


        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($author, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/author-list/{id}', name: 'app_author_list', methods: ['GET'])]
    public function list_authors(Request $request, string $id): JsonResponse
    {
        $data = $this->entityManager->getRepository(Author::class)->findOneBy($id);

        $jsonData = $this->serializer->deserialize($request->getContent(), Author::class, 'json');
    }
}
