<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $dbLogger)
    {
        $this->logger = $dbLogger;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/requete/{postId}", name="requete")
     */
    public function requete($postId, Request $request)
    {
        $url = $request->getPathInfo(); //Récupère l'url
        // récupérer des attributs en GET ou POST
        $name = $request->get('name');

        //$name = $request->query->get('name', 'test');
        $method = $request->getMethod();    // e.g. GET, POST, PUT, DELETE ou HEAD
        return $this->render('home/requete.html.twig', [
            'param1' => $postId,
            'url' => $url,
            'nom' => $name,
            'method' => $method
        ]);
    }

    /**
     * @Route("/reponse", name="reponse")
     */
    public function reponse(Request $request)
    {
        $request = Request::createFromGlobals();
        $name = $request->get('name');
        $response = new Response();

        $response->setContent(
            '<html><body>Hello'
            . $name
            . '</body></html>'
        );
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/html');

        // Retourne une réponse HTTP valide
        
        return $this->render('home/reponse.html.twig', [
            'reponse' => $response->send(),
        ]);
    }

}
