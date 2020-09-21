<?php 
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class RepositorySearch
{
    /**
     * Name of the repository
     * @Assert\NotBlank
     * @var string
     */
    private $search;
    
    /**
     * Get the value of search
     */ 
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set the value of search
     *
     * @return  self
     */ 
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }
}
?>