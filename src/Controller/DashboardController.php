<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\RepositorySearchType;
use App\Entity\RepositorySearch;
use App\Entity\User as EntityUser;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(HttpClientInterface $httpClient, Request $request, Security $security, PaginatorInterface $paginator)
    {
        $request = Request::createFromGlobals();
        $token = $request->cookies->get('token'); //Récuperation du cookie

        //Les repositories du user
        $response = $httpClient->request('GET', 'https://api.github.com/users/'.$security->getUser()->getUsername().'/repos', [
            'query' => [
                'sort' => 'created',
            ]
        ]);

        //Formulaire de recherche repository
        $search = new RepositorySearch();
        $form = $this->createForm(RepositorySearchType::class, $search);
        $form = $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Les repositories du user
            $searchRepository = $httpClient->request('GET', 'https://api.github.com/repos/' .$security->getUser()->getUsername(). '/' . $search->getSearch(), [
                'headers' => [
                    'Authorization' => "token " . $token
                ]
            ]);

            return $this->redirectToRoute('dashboard_show', [
                'id' => $searchRepository->toArray()['id']
            ]);
        }


        //Pagination
        $repositories = $paginator->paginate(
            $response->toArray(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('dashboard/index.html.twig', [
            'form' => $form->createView(),
            'repositories' => $repositories,
            'AllRepository' => $response->toArray()
        ]);

    }

    /**
     * @Route("/dashboard/show/{id}", name="dashboard_show")
     */
    public function show($id, HttpClientInterface $httpClient, Request $request, Security $security)
    {
        $request = Request::createFromGlobals();
        $token = $request->cookies->get('token'); //Récuperation du cookie

        //Récupération de l'objet user du namespace entity dans la bdd
        $userBdd = $this->getDoctrine()->getRepository(EntityUser::class)->findOneByUsername($security->getUser()->getUsername());
        //Récupération de la table contact en fonction de son user
        $contactByUserIdBdd = $this->getDoctrine()->getRepository(Contact::class)->findByUser($userBdd->getId());


        //Le repository spécifique à $id
        $response = $httpClient->request('GET', 'https://api.github.com/repositories/' . $id, [
            'headers' => [
                'Authorization' => "token " . $token
            ]
        ]);

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException(sprintf('No repository with id %s', $id));
        }

        //Les derniers commits éffectués
        $commit = $httpClient->request('GET', 'https://api.github.com/repos/'.$response->toArray()['full_name'].'/commits', [
            'headers' => [
                'Authorization' => "token " . $token
            ]
        ]);

        //Les derniers évenement du repository
        $lastChange = $httpClient->request('GET', 'https://api.github.com/repos/' . $response->toArray()['full_name'] . '/events', [
            'headers' => [
                'Authorization' => "token " . $token
            ]
        ]);

        //Création de formulaire email notification
        $contact = new Contact();
        $contact->setUser($userBdd);
        $contact->setRepository($response->toArray()['name']);

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($contactByUserIdBdd != null) {
                
                //Parcourt le tableau de contact
                foreach ($contactByUserIdBdd as $contactUser) {
                    //Test si un élément éxiste déjà dans la table contact
                    if ($contactUser->getEmail() == $contact->getEmail()) {

                        $this->addFlash('danger', 'Email déjà enregistré pour ce repository');

                        return $this->redirectToRoute('dashboard_show', [
                            'id' => $id
                        ]);
                    }
                }
            }

            //Ajout dans la bdd
            $this->manager->persist($contact);
            $this->manager->flush($contact);

            $this->addFlash('success', 'Email enregistré');
            
            return $this->redirectToRoute('dashboard_show', [
                'id' => $id
            ]);
        }

        /*if ($contactByUserIdBdd != null) {
            
            $contactBdd = new Contact();
            $contactBdd->setEmail($contactByUserIdBdd->getEmail());
            $contactBdd->setRepository($contactByUserIdBdd->getRepository());
            $contactBdd->setUser($contactByUserIdBdd->getUser());
            
            //Déclanche l'evenement
            $event = new GithubRepositoryEvent($contactBdd, $lastChange->toArray());

            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch($event, GithubRepositoryEvent::NAME);
            }
        }*/

         
        return $this->render('dashboard/show.html.twig', [
            'repo' => $response->toArray(),
            'commits' => $commit->toArray(),
            'lastChanges' => $lastChange->toArray(),
            'form' => $form->createView()
        ]);
    }

}
