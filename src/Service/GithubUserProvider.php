<?php 
namespace App\Service;

use App\Security\User;
use App\Entity\User as EntityUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubUserProvider
{
    private $githubId;
    private $githubSecret;
    private $httpClient;
    private $em;
    private $repository;

    public function __construct($githubId, $githubSecret, HttpClientInterface $httpClient, EntityManagerInterface $em, UserRepository $repository)
    {
        $this->githubId = $githubId;
        $this->githubSecret = $githubSecret;
        $this->httpClient = $httpClient;
        $this->em = $em;
        $this->repository = $repository;
    }

    public function loadUserFromGithub(string $code)
    {
        $request = Request::createFromGlobals();
         
        //Si le cookie existe
        if ($request->cookies->has('token')) {
            $token = $request->cookies->get('token');
        }
        else {

            $url = sprintf(
                "https://github.com/login/oauth/access_token?client_id=%s&client_secret=%s&code=%s",
                $this->githubId,
                $this->githubSecret,
                $code
            );

            //Appelle Oauth de github pour reçevoir l'access_token
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'Accept' => "application/json"
                ]
            ]);

            //Initalisation de $token
            $token = $response->toArray()['access_token'];

            //Création du cookie
            $res = new Response();
            $cookie = Cookie::create('token')
                ->withValue($response->toArray()['access_token'])
                ->withExpires(strtotime("+7 hours"));

            $res->headers->setCookie($cookie);
            $res->send();
        }

        //Appelle l'api avec $token
        $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => "token " . $token
            ]
        ]); 

        $data = $response->toArray();
        $userBdd = $this->repository->findOneByUsername($data['login']);

        //Test si l'utilisateur éxiste déjà en bdd
        if ($userBdd == null) {
            $user = new EntityUser();
            $user->setUsername($data['login']);
            $user->setRoles(['ROLE_USER']);

            $this->em->persist($user);
            $this->em->flush($user);
        }
        
        return new User($data);
    }
}
?>