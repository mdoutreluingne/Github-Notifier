<?php 
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

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

    /**
     * Undocumented function
     *
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        //On modifie le record pour ajouter nos infos
        $record['extra']['clientIp'] = $this->request->getClientIp();
        $record['extra']['url'] = $this->request->getBaseUrl();
        $user = $this->security->getUser();
        $record['extra']['user'] = $user->getUsername();

        return $record;
    }

    
}
?>