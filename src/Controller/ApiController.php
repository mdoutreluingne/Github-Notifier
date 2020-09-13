<?php

namespace App\Controller;

use App\Form\RepoSearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(HttpClientInterface $httpClient, Request $request)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/users/mdoutreluingne/repos', [
            'query' => [
                'sort' => 'created',
            ],
        ]);

        $form = $this->createForm(RepoSearchType::class);
        $form = $form->handleRequest($request);
        
        /*if ($form->isSubmitted() && $form->isValid()) { 
            
        }*/
        
        return $this->render('api/index.html.twig', [
            'repos' => $response->toArray(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/api/show/{id}", name="show")
     */
    public function show($id, HttpClientInterface $httpClient)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/repositories/'.$id);

        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException(sprintf('No repo with id %s', $id));
        }
        return $this->render('api/show.html.twig', [
            'repo' => $response->toArray(),
        ]);
    }
}
