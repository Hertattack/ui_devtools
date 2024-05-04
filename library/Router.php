<?php

namespace library;

use controllers\SampleController;

class Router
{
    public function __construct(){
    }

    public function RouteRequest(){
        $dispatcher = new Dispatcher();

        $this->PerformRequest($dispatcher);
    }

    private function PerformRequest($dispatcher){
        $session = new Session();
        $request = new Request();
        $response = new Response();

        $controller = new SampleController();
        $controller->session = $session;
        $controller->request = $request;
        $controller->response = $response;

        $controller->initialize();

        $controller->performAction();

        if($response->getHeaders()->get("Status") != null)
            return;
    }
}