<?php

namespace library;

class Router
{
    private string $prefix;
    private int $prefixLength;

    public function __construct($prefix){
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

            $this->PerformRequest($dispatcher, $queryParams);

        }catch(\Exception $ex){
            echo $ex->getMessage();
        }
    }

    private function PerformRequest(Dispatcher $dispatcher, $queryParams){
        $session = new Session();
        $request = new Request($queryParams);
        $response = new Response();

        $dispatcher->Dispatch($request, $response, $session);

        if($response->getHeaders()->get("Status") != null)
            return;
    }

    private function ParsePath($path) : array {
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
                $result["action"] = $this->EnforceCamelCase($element);
            }else{
                $orderedParameters[] = $element;
            }
            $count++;
        }

        return $result;
    }

    private function EnforceCamelCase($value) : string {
        if(stripos($value, "_") === 0)
            return $value;

        $camelCaseResult = "";
        foreach(explode('_', $value) as $part){
            $camelCaseResult .= ucfirst($part);
        }

        return $camelCaseResult;
    }
}