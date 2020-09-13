<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $dbLogger)
    {
        $this->mailer = $mailer;
        $this->logger = $dbLogger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10],
                ['notifyException', 15],
            ],
        ];
    }

    public function processException(ExceptionEvent $event)
    {
       //dd($event);

       /*switch (true) {
           case ($event->getThrowable()->getStatusCode() > 400 && $event->getThrowable()->getStatusCode() < 500):
                $this->logger->error($event->getThrowable()->getMessage());
               break;

            case ($event->getThrowable()->getStatusCode() > 200 && $event->getThrowable()->getStatusCode() < 250):
                $this->logger->info($event->getThrowable()->getMessage());
                break;
       }*/
    }

    public function notifyException(ExceptionEvent $event)
    {
        /*$email = (new Email())
            ->from('helsslo@example.com')
            ->to('yodddu@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Une exception a été relevé')
            ->html('<p>'.$event->getThrowable()->getMessage().' avec une erreur ' .$event->getThrowable()->getStatusCode(). '</p>');

        $this->mailer->send($email);*/
    }
}
