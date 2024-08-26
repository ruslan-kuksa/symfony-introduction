<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookingController extends AbstractController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $entityManager,
    )
    {}
    #[Route('/api/booking', name: 'app_booking', methods: ['POST'])]
    public function booking(Request $request): Response
    {
        $booking = $this->serializer->deserialize($request->getContent(), Booking::class, 'json');
        $data = json_decode($request->getContent(), true);

        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);
        $user = $this->entityManager->getRepository(User::class)->find($data['user_id']);

        $booking->setBook($book);
        $booking->setUser($user);
        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        $jsonData = $this->serializer->serialize($booking, 'json');

        return new JsonResponse($jsonData, Response::HTTP_CREATED, [], true);

    }
}
