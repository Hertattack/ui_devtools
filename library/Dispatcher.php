<?php

namespace library;

use mysql_xdevapi\Exception;
use ReflectionClass;

class Dispatcher
{
    private string $namespace;
    private string $controller;
    private string $action;
    private array $parameters;
    private ReflectionClass $controllerClass;
    private \ReflectionMethod $actionMethod;

    private bool $isExecutable = false;

    public function __construct($namespace, $controller, $action, $parameters)
    {
        $this->namespace = $namespace;
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
    }

    public function CanExecute() : bool {
        try {
            $this->controllerClass = new ReflectionClass($this->namespace . "\\" . $this->controller);
            if(!$this->controllerClass->isInstantiable())
                return false;
            echo " a ";
            $this->actionMethod = $this->controllerClass->getMethod($this->action . "Action");
            echo " b ";
            if(count($this->actionMethod->getParameters()) !=  count($this->parameters))
                return false;

            return $this->isExecutable = true;
        }catch(\Exception $ex){
            echo $ex->getMessage();
            return false;
        }
    }

    public function Dispatch($request, $response, $session) : void {
        if(!$this->isExecutable && !$this->CanExecute())
            throw new \Exception("Cannot dispatch");

        $controller = $this->controllerClass->newInstance();
        $controller->session = $session;
        $controller->request = $request;
        $controller->response = $response;

        $controller->initialize();

        $this->ExecuteBeforeRequestAction($controller);

        $this->actionMethod->invoke($controller, ... $this->parameters);

        $this->ExecuteAfterRequestAction($controller);
    }

    private function ExecuteBeforeRequestAction($controller) : void {
        foreach($this->controllerClass->getMethods() as $methodInfo){
            if($methodInfo->getName() == "beforeExecuteRoute") {
                $methodInfo->invoke($controller, $this);
                return;
            }
        }
    }

    private function ExecuteAfterRequestAction($controller) : void {
        foreach($this->controllerClass->getMethods() as $methodInfo){
            if($methodInfo->getName() == "afterExecuteRoute") {
                $methodInfo->invoke($controller, $this);
                return;
            }
        }
    }
}