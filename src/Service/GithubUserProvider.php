<?php 
namespace App\Service;

use App\Security\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubUserProvider
{
    private $githubId;
    private $githubSecret;
    private $httpClient;
    public $username;
    private $email;
    private $token;

    public function __construct($githubId, $githubSecret, HttpClientInterface $httpClient, string $username=null, string $email=null)
    {
        $this->githubId = $githubId;
        $this->githubSecret = $githubSecret;
        $this->httpClient = $httpClient;
        $this->username = $username;
        $this->email = $email;
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
        //dd($data);
        $this->username = $data['login']; //Récupération du login github
        $this->email = $data['login']; //Récupération de l'email du user github
        
        return new User($data);
    }
}
?>