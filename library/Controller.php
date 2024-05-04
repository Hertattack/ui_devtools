<?php

namespace library;

abstract class Controller
{
    public Session $session;
    public Request $request;
    public Response $response;

    public function initialize(){}
}