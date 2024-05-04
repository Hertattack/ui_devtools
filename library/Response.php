<?php

namespace library;

class Response
{
    private Headers $headers;

    public function __construct(){
        $this->headers = new Headers();
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }
}