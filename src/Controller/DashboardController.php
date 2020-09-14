<?php

namespace App\Controller;

use App\Form\RepoSearchType;
use App\Service\GithubUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardController extends AbstractController
{
    private $dataProvider;

    public function __construct(GithubUserProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(HttpClientInterface $httpClient, Request $request)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/users/'. $this->dataProvider->username .'/repos', [
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
    public function show($id, HttpClientInterface $httpClient)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/repositories/' . $id);
        $commit = $httpClient->request('GET', 'https://api.github.com/repos/'.$response->toArray()['full_name'].'/commits');

        //dd($commit->toArray());

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException(sprintf('No repo with id %s', $id));
        }
        return $this->render('dashboard/show.html.twig', [
            'repo' => $response->toArray(),
            'commits' => $commit->toArray()
        ]);
    }
}
