<?php

namespace App\FormHandler;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailHandler
{
    public function __construct(private MailerInterface $mailer){}
    public function sendEmail(User $user):void
    {
        $email = (new TemplatedEmail())
            ->from('loopbox@gmail.com')
            ->to($user->getEmail())
            ->subject('Time for Symfony Mailer!')
            ->htmlTemplate('emails/confirmation.html.twig')
            ->context([
                'userName'=>$user->getName(),
            ]);

        $this->mailer->send($email);

    }

}
