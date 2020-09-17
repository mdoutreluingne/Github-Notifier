<?php

namespace App\Event;

use Acme\Store\Order;
use App\Entity\Contact;
use App\Service\GithubRepositoryProvider;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class GithubRepositoryEvent extends Event
{
    public const NAME = 'repository.event';

    private $data;

    public function __construct(Contact $data)
    {
        //$this->data = $data->callEventRepositoryFromGithub('mdoutreluingne/Github-notifier');
        $this->data = $data;
    }


    /**
     * Get the value of data
     */ 
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */ 
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}

?>