<?php

namespace SomeNiceNamespace;

use library\Controller;

class YoloController extends Controller
{
    public function doSomeNiceAction($value1, $value2){
        echo "Woooohoooooo<br>";
        echo "Value 1: " . $value1 . "<br>";
        echo "Value 2: " . $value2 . "<br>";
    }
}