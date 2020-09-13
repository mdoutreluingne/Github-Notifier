<?php 
namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class DbHandler extends AbstractProcessingHandler
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function write(array $record): void
    {
        //Envoie des logs dans la base de données
        $log = new Log();
        $log->setContext($record['context']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setMessage($record['message']);
        $log->setExtra($record['extra']);
        $log->setUser($record['extra']['user']);

        $this->manager->persist($log);
        $this->manager->flush($log);
    }
}
?>