<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\RepoSearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{
    private $user;
    private $manager;

    public function __construct(Security $user, EntityManagerInterface $manager)
    {
        $this->user = $user->getUser();
        $this->manager = $manager;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(HttpClientInterface $httpClient, Request $request)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/users/'. $this->user->getUsername() .'/repos', [
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
    public function show($id, HttpClientInterface $httpClient, Request $request)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/repositories/' . $id);
        $commit = $httpClient->request('GET', 'https://api.github.com/repos/'.$response->toArray()['full_name'].'/commits');
        $lastChange = $httpClient->request('GET', 'https://api.github.com/repos/' . $response->toArray()['full_name'] . '/events');


        //dd($lastChange->toArray());

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException(sprintf('No repository with id %s', $id));
        }

        //Création de formulaire email notification
        $contact = new Contact();
        $contact->setUser($this->user->getUsername());
        $contact->setRepository($response->toArray()['name']);

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->manager->persist($contact);
            $this->manager->flush($contact);

            $this->addFlash('success', 'Email enregistré');

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
