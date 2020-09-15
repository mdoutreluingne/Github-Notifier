<?php 
namespace App\Notification;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ContactNotification extends AbstractController{

    private $mailer;
    private $renderer;

    public function __construct(MailerInterface $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Contact $contact)
    {
        $email = (new Email())
            ->from($contact->getEmail())
            ->to('contact-portfolio@maxime-doutreluingne.yj.fr')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            ->replyTo($contact->getEmail())
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Contact portfolio')
            ->html($this->renderer->render('contact/contact.html.twig', [
                'contact' => $contact
            ]), 'text/html');

        $this->mailer->send($email);
    }
}

?>