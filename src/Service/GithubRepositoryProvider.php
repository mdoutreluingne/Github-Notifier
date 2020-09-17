<?php 

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class GithubRepositoryProvider
{

    protected $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function callEventRepositoryFromGithub(string $fullname)
    {
        $data = $this->httpClient->request('GET', 'https://api.github.com/repos/' . $fullname . '/events');
        return $data->toArray();
    }

}

?>