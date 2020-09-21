<?php

namespace App\Command;

use App\Repository\ContactRepository;
use App\Service\MailerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class CallApiGithubCommand extends Command
{
    protected static $defaultName = 'app.api.github';
    private $httpClient;
    private $contactRepo;
    private $mailer;

    public function __construct(HttpClientInterface $httpClient, ContactRepository $contactRepo, MailerService $mailer)
    {
        $this->httpClient = $httpClient;
        $this->contactRepo = $contactRepo;
        $this->mailer = $mailer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Call api github')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Récupération de la table contact
        $contactByUserIdBdd = $this->contactRepo->findAll();

        //Envoie chaque email pour les utilisateurs abonné
        foreach ($contactByUserIdBdd as $contact) {
            //Les derniers évenement du repository
            $lastChange = $this->httpClient->request('GET', 'https://api.github.com/repos/' . $contact->getUser()->getUsername() . '/' . $contact->getRepository() . '/events');


            $this->mailer->send($contact->getEmail(), 'noreplay@gmail.com', 'Une exception a été relevé', 'contact/contact.html.twig', [
                'contact' => $contact,
                'dataEventRepository' => $lastChange->toArray()
            ]);
        }

        return 0;
    }
}
