<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        $exception = $event->getThrowable();

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            switch (true) {
                case ($exception->getStatusCode() > 400 && $exception->getStatusCode() < 500):
                    $this->logger->error($exception->getMessage());
                    break;

                case ($exception->getStatusCode() > 200 && $exception->getStatusCode() < 250):
                    $this->logger->info($exception->getMessage());
                    break;
            }
        } else {
            $this->logger->info($exception->getMessage());
        }
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
            ->html('<p>'.$event->getThrowable()->getMessage().' avec une erreur </p>');

        $this->mailer->send($email);*/
    }
}
