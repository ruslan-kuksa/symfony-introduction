<?php

namespace App\EntityListener;

use App\Entity\Booking;
use App\Entity\User;
use App\Services\MailerService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
class BookingEntityListener
{
    /**
     * @param MailerService $mailerService
     */
    public function __construct(private MailerService $mailerService){}

    /**
     * @param User $user
     * @param LifecycleEventArgs $eventArgs
     * @return void
     */
    public function prePersist(Booking $booking, LifecycleEventArgs $eventArgs): void
    {
        $user = $booking->getUser();
        $book = $booking->getBook();


        $booking = [
            'date' => $booking->getDate()->format('Y-m-d'),
            'status' => $booking->getStatus(),
        ];

        $userReceiver = [
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surName' => $user->getSurname(),
        ];
        $book = [
            'title' => $book->getTitle(),
            'name' => $book->getName(),
            'filepath' => $book->getFilepath(),
            'wrote_at' => $book->getWroteAt()->format('Y-m-d'),
            'text' => $book->getText(),
        ];
        $Info = [
            'to' => $userReceiver['name'].' '.$userReceiver['surName'],
            'book' => $book['name'].' '.$book['wrote_at'].' '.$book['text'].' '.$book['title'].' '.$book['filepath'],
            'booking' => $booking['date'].' '.$booking['status'],
            'text' => 'Thanks for purchase',
        ];

        //Send mail to Receiver
        $this->mailerService->SendMailFunc($userReceiver, $Info);
    }
}