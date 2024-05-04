<?php

namespace controllers;

use library\Controller;
use library\Request;
use library\Response;
use library\Session;

class SampleController extends Controller
{

    public Session $session;
    public Request $request;
    public Response $response;

    public function performAction(){
        print("Hi from here!");
    }
}