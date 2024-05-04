<?php

namespace library;

use controllers\SampleController;

class Router
{
    private string $prefix;
    private int $prefixLength;

    public function __construct($prefix, $hasNamespace = false){
        $this->prefix = $prefix;
        $this->prefixLength = strlen($prefix);
    }

    public function RouteRequest($uri){
        try{
            $urlParts = parse_url($uri);
            $path  = $urlParts['path'];

            if(!str_starts_with($path, $this->prefix))
                throw new \Exception("Invalid route path: " . $uri);

            $path = substr($path, $this->prefixLength);
            $targetAndParameters = $this->parsePath($path);

            $namespace = $targetAndParameters['namespace'];
            $controller = $targetAndParameters['controller'];
            $action = $targetAndParameters['action'];
            $parameters = $targetAndParameters['orderedParameters'];

            if($action === null || $controller === null || $namespace === null)
                throw new \Exception("Invalid route path, no action, controller, and / or namespace: " . $uri);

            $query = $urlParts['query'];
            if($query > "")
                parse_str($query, $queryParams);
            else
                $queryParams = [];

            $dispatcher = new Dispatcher($namespace, $controller, $action, $parameters);
            if(!$dispatcher->CanExecute())
                throw new \Exception("Cannot dispatch request for some reason :-) " . $uri);

        }catch(Exception $ex){
            // Return error result
            echo "NOOOOOO";
            echo $ex->getMessage();
        }
    }

    private function PerformRequest($dispatcher){
        $session = new Session();
        $request = new Request();
        $response = new Response();

        $controller = new SampleController();
        $controller->session = $session;
        $controller->request = $request;
        $controller->response = $response;

        $request = new Request();

        $controller->initialize();

        $controller->performAction();

        if($response->getHeaders()->get("Status") != null)
            return;
    }

    private function ParsePath($path) : Array {
        $pathElements = explode("/", $path);
        $orderedParameters = [];
        $result = Array(
            "namespace" =>null,
            "controller" =>null,
            "action" =>null,
            "orderedParameters" => &$orderedParameters
        );

        $count = 0;
        foreach($pathElements as $element){
            if($count == 0) {
                $result["namespace"] = $element;
            } else if($count == 1) {
                $result["controller"] = $element;
            } else if($count == 2) {
                $result["action"] = $element;
            }else{
                $orderedParameters[] = $element;
            }
            $count++;
        }

        return $result;
    }
}