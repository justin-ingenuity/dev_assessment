<?php

namespace App\Service;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class SignUpNotificationService
{
    private $mailer;
    private $senderEmail;
    private $adminEmail;

    public function __construct(MailerInterface $mailer, $senderEmail, $adminEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->adminEmail = $adminEmail;
    }

    public function sendNotification(Contact $contact)
    {
        $email = (new TemplatedEmail())
            ->from($this->senderEmail)
            ->to($contact->getEmail())
            ->subject('Thanks for signing up!')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'name' => sprintf('%s %s', $contact->getFirstName(), $contact->getLastName()),
            ]);

        $this->mailer->send($email);

        $email = (new TemplatedEmail())
            ->from($this->senderEmail)
            ->to($this->adminEmail)
            ->subject('New signing up!')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'name' => sprintf('%s %s', $contact->getFirstName(), $contact->getLastName()),
            ]);

        $this->mailer->send($email);
    }
}