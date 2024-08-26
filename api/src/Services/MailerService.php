<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(private MailerInterface $mailer){}

    /**
     * @param $user
     * @param $info
     * @return void
     */
    public function SendMailFunc($user, $info):void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@inbox.mailtrap.io')
            ->to($user['email'])
            ->subject('Тестовий меіл !')
            ->htmlTemplate('mailTemplate.twig')
            ->context([
                'to' => $info['to'],
                'book' => $info['book'],
                'booking' => $info['booking'],
                'text' => $info['text'],
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $e->getMessage();
        }
    }
}