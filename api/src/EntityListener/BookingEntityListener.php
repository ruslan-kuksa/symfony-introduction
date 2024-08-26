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
    public function prePersist(User $user, Booking $booking, LifecycleEventArgs $eventArgs): void
    {
        $bookingReceiver = [
            'date' => $booking->getDate()->format('Y-m-d'),
            'book' => $booking->getBook(),
            'user' => $booking->getUser(),
            'status' => $booking->getStatus(),
        ];

        $userReceiver = [
            'userEmail' => $user->getEmail(),
            'name' => $user->getName(),
            'surName' => $user->getSurname(),
        ];

        $userInfo = [
            'from' => $userReceiver['name'].' '.$userReceiver['surName'],
            'text' => 'Тестова пошта для тесту',
        ];

        //Send mail to Receiver
        $this->mailerService->SendMailFunc($bookingReceiver, $userReceiver, $userInfo);
    }
}