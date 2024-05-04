<?php

namespace library;

use ReflectionClass;

class Dispatcher
{
    private string $namespace;
    private string $controller;
    private string $action;
    private string $parameters;

    public function __construct($namespace, $controller, $action, $parameters)
    {
        $this->namespace = $namespace;
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
    }

    public function CanExecute() : bool {
        try {
            $reflectionClass = new ReflectionClass($this->namespace . "\\" . $this->controller);
            if(!$reflectionClass->isInstantiable())
                return false;

            
        }catch(\Exception $ex){
            return false;
        }
    }
}