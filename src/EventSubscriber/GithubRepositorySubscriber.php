<?php

namespace App\EventSubscriber;

use App\Event\GithubRepositoryEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


class GithubRepositorySubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            GithubRepositoryEvent::NAME => 'SendLastEventRepository',
        ];
    }

    public function SendLastEventRepository(GithubRepositoryEvent $event)
    {
        //Récupération de l'oject contact
        $dataContact = $event->getContact();
        $event->setContact($dataContact);

        //Récupération des derniers event du repository
        $dataEventRepository = $event->getLastEventRepository();
        $event->setLastEventRepository($dataEventRepository);

        $email = (new TemplatedEmail())
            ->from($dataContact->getEmail())
            ->to('yodddu@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Une exception a été relevé')
            ->htmlTemplate('contact/contact.html.twig')
            ->context([
                'dataContact' => $dataContact,
                'dataEventRepository' => $dataEventRepository
            ]);

        $this->mailer->send($email);
    }
}
