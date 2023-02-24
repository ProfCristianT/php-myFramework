<?php
namespace vendor\middlewares;

use vendor\abstracts\Middleware;
use vendor\interfaces\iMiddleware;
use vendor\classes\Request;

class DefaultRouter extends Middleware implements iMiddleware{
    private array $routes = [
        //[ "method"=>"GET", "uri"=>"/products", "callback"=function(){} ]
    ];

    public function __construct(){}

    private function addRoute($method, $uri, $callback){
        array_push( $this->routes ,  [  
                "method"=> strtoupper($method), 
                "uri"=>$uri, 
                "callback"=>$callback
        ]);
    }
    
    public function get($uri, $callback){
        $this->addRoute("GET", $uri, $callback);
    }

    public function post($uri, $callback){
        $this->addRoute("POST", $uri, $callback);
    }

    public function put($uri, $callback){
        $this->addRoute("PUT", $uri, $callback);
    }

    public function patch($uri, $callback){
        $this->addRoute("PATCH", $uri, $callback);
    }

    public function delete($uri, $callback){
        $this->addRoute("DELETE", $uri, $callback);
    }


    public function handle( Request $request){
        //var_dump($request);

        foreach ($this->routes as $route) {
            // "/products/:id"
            $regExp = '/^' . str_replace('/', '\/', $route["uri"])  . '$/';
            // "/^\/products\/:id$/"
            $regExp = preg_replace_callback("/:(\w+)/", function($matches){
                return "(?<$matches[1]>[\w\-\.]+)";
            }, $regExp);
            // "/products/(?<id>[\w\-\.]+)"
            

            //var_dump($route);
            if( $route["method"] === $request->getMethod() 
                && preg_match($regExp, $request->getUri(), $matches ) ){
 
                $request->params = $matches;

                $route["callback"]($request);
            }
        }
    }
}

