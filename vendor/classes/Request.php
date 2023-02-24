<?php

namespace vendor\classes;

class Request{
    public array $params;
    
    public function __construct(
        private string $method,
        private string $uri
    ){}

    public function getMethod(){
        return $this->method;
    }
    
    public function getUri(){
        return $this->uri;
    }



    public static function getStandard(){
        return new Request($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
    }

    public static function getCustom($start = 0){

        $splitUri = explode( "/", $_SERVER["REQUEST_URI"] );
        array_splice($splitUri, 0, $start);
        $uri = "/" . implode("/", $splitUri);
        
        return new Request($_SERVER["REQUEST_METHOD"], $uri) ;
    }


}
