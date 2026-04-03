<?php
declare(strict_types=1);

namespace App\Controller;

class PingController extends AppController
{
    public function index()
    {
        $this->response = $this->response->withType("application/json");
        $this->response->getBody()->write(json_encode([
            "success" => true,
            "message" => "Pong!"
        ]));
        return $this->response;
    }
}