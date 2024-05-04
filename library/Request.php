<?php

namespace library;

class Request
{
    public array $queryParameters;

    public function __construct($queryParameters) {
        $this->queryParameters = $queryParameters;
    }
}