<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(HttpClientInterface $httpClient)
    {
        $response = $httpClient->request('GET', 'https://api.github.com/users/mdoutreluingne/repos', [
            'query' => [
                'sort' => 'created',
            ],
        ]);
        return $this->render('api/index.html.twig', [
            'repos' => $response->toArray(),
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
