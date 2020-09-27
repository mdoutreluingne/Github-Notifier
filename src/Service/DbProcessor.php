<?php 
namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class DbProcessor
{
    private $request;
    private $security;

    /**
     * Construcut
     *
     * @param RequestStack $request
     * @param Security $security
     */
    public function __construct(RequestStack $request, Security $security)
    {
        $this->request = $request->getCurrentRequest();
        $this->security = $security;
    }

    public function __invoke(array $record)
    {
        //On modifie le record pour ajouter nos infos
        $record['extra']['clientIp'] = $this->request->getClientIp();
        $record['extra']['url'] = $this->request->getBaseUrl();        
        $record['extra']['user'] = $this->security->getUser();

        return $record;
    }

    
}
?>