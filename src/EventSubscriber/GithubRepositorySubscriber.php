<?php

namespace App\EventSubscriber;

use Symfony\Component\Mime\Email;
use App\Event\GithubRepositoryEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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
            GithubRepositoryEvent::NAME => 'onFilterApi',
        ];
    }

    public function onFilterApi(GithubRepositoryEvent $event)
    {
        $data = $event->getData();
        $event->setData($data);

        $email = (new Email())
            ->from('helsslo@example.com')
            ->to('yodddu@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Une exception a été relevé')
            ->html('<p>dddd</p>');

        $this->mailer->send($email);
    }
}
