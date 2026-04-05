<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        
        $this->autoRender = false;
        
        // Configura para JSON
        $this->response = $this->response->withHeader('Content-Type', 'application/json');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
    }
}