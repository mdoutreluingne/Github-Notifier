<?php

namespace App\Event;

use App\Entity\Contact;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class GithubRepositoryEvent extends Event
{
    public const NAME = 'repository.event';

    private $contact;
    private $lastEventRepository;

    public function __construct(Contact $contact, array $lastEventRepository)
    {
        $this->contact = $contact;
        $this->lastEventRepository = $lastEventRepository;
    }


    /**
     * Get the value of contact
     */ 
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set the value of contact
     *
     * @return  self
     */ 
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }


    /**
     * Get the value of lastEventRepository
     */ 
    public function getLastEventRepository()
    {
        return $this->lastEventRepository;
    }

    /**
     * Set the value of lastEventRepository
     *
     * @return  self
     */ 
    public function setLastEventRepository($lastEventRepository)
    {
        $this->lastEventRepository = $lastEventRepository;

        return $this;
    }
}

?>