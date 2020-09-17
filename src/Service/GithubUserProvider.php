<?php 
namespace App\Service;

use App\Entity\User as EntityUser;
use App\Repository\UserRepository;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubUserProvider
{
    private $githubId;
    private $githubSecret;
    private $httpClient;
    private $token;
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
        $url = sprintf("https://github.com/login/oauth/access_token?client_id=%s&client_secret=%s&code=%s",
        $this->githubId, $this->githubSecret, $code);

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Accept' => "application/json"
            ]
        ]);

        $token = $response->toArray()['access_token'];
        //Gere erreur si le token est introuvable alors return null puis leve exception
        //TODO 

        $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => "token ". $token
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