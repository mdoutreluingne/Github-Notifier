<?php

namespace App\EventSubscriber;

use App\Event\GithubRepositoryEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


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
        $dataUser = $event->getUser();
        $event->setUser($dataUser);

        $email = (new TemplatedEmail())
            ->from('dfdfsf@dsfdsf.fr')
            ->to('noreplay@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Welcome to Github-notifier')
            ->htmlTemplate('contact/register.html.twig')
            ->context([
                'dataUser' => $dataUser
            ]);

        $this->mailer->send($email);
    }
}
