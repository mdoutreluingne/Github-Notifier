<?php 
namespace App\EventListener;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DoctrineSubscriber implements EventSubscriber
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
       $this->mailer = $mailer;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postRemove,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        //$this->log('ajoutée', $args);
        return null;
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        //$this->log('supprimé', $args);
       return null;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        //$this->log('modifié', $args);
        return null;
    }

    public function log($message, $args)
    {
        /*$entity = $args->getEntity();
        if (!$entity instanceof MailerInterface) {
            $email = (new Email())
                ->from('hello@example.com')
                ->to('you@example.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Ajout d\'une donnée')
                ->html('<p>' . $entity->getEmail() . " " . $message .'</p>');

            $this->mailer->send($email);
        }*/
        return null;
        
    }
}
?>