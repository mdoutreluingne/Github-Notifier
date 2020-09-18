<?php

namespace App\Controller;

use App\Security\User;
use App\Entity\Contact;
use App\Entity\User as EntityUser;
use App\Form\ContactType;
use App\Form\RepoSearchType;
use App\Event\GithubRepositoryEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\GithubRepositoryProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardController extends AbstractController
{
    private $manager;
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $manager, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(HttpClientInterface $httpClient, Request $request, Security $security)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/users/'. $security->getUser()->getUsername() .'/repos', [
            'query' => [
                'sort' => 'created',
            ],
        ]);

        $form = $this->createForm(RepoSearchType::class);
        $form = $form->handleRequest($request);

        /*if ($form->isSubmitted() && $form->isValid()) { 
            
        }*/

        return $this->render('dashboard/index.html.twig', [
            'repos' => $response->toArray(),
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/dashboard/show/{id}", name="dashboard_show")
     */
    public function show($id, HttpClientInterface $httpClient, Request $request, Security $security)
    {
        /*$event = new GithubRepositoryEvent($githubRepositoryProvider);
        return $this->json($event->getData());*/
        //$dispatcher->dispatch($event, GithubRepositoryEvent::NAME);

        $userBdd = $this->getDoctrine()->getRepository(EntityUser::class)->findOneByUsername($security->getUser()->getUsername());

        $response = $httpClient->request('GET', 'https://api.github.com/repositories/' . $id);
        $commit = $httpClient->request('GET', 'https://api.github.com/repos/'.$response->toArray()['full_name'].'/commits');
        $lastChange = $httpClient->request('GET', 'https://api.github.com/repos/' . $response->toArray()['full_name'] . '/events');


        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException(sprintf('No repository with id %s', $id));
        }

        //Création de formulaire email notification
        $contact = new Contact();
        $contact->setUser($userBdd);
        $contact->setRepository($response->toArray()['name']);

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->manager->persist($contact);
            $this->manager->flush($contact);

            $this->addFlash('success', 'Email enregistré');

            $test = new Contact();
            $event = new GithubRepositoryEvent($test);

            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch($event, GithubRepositoryEvent::NAME);
            }

            return $this->redirectToRoute('dashboard_show', [
                'id' => $id
            ]);
        }

        
        
        return $this->render('dashboard/show.html.twig', [
            'repo' => $response->toArray(),
            'commits' => $commit->toArray(),
            'lastChanges' => $lastChange->toArray(),
            'form' => $form->createView()
        ]);
    }

}
