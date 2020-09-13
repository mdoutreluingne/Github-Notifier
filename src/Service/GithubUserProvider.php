<?php 
namespace App\Service;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubUserProvider
{
    private $githubId;
    private $githubSecret;
    private $httpClient;

    public function __construct($githubId, $githubSecret, HttpClientInterface $httpClient)
    {
        $this->githubId = $githubId;
        $this->githubSecret = $githubSecret;
        $this->httpClient = $httpClient;
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
        //Gere erreur si le token est introuvable alors return null
        //TODO 

        $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => "token ". $token
            ]
        ]);

        $data = $response->toArray();

        return new User($data);
    }
}
?>