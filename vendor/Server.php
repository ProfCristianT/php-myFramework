<?php 
namespace vendor;

use Exception;
use vendor\classes\Request;
use vendor\abstracts\Middleware;
use vendor\middlewares\ExceptionManager;
use vendor\middlewares\DataInputParser;

class Server {
    private static bool $instance = false;
    private array $middlewares = [];

    public function __construct(){
        if( self::$instance ){
            throw new Exception("Ilegal constructor: Server is a Singleton");
        }

        //self::$instance = $this; //guardando la primer instancia en la prop estatica
        self::$instance = true; //guardando la primer instancia en la prop estatica
    }

    public function use(Middleware $middleware){
        array_push( $this->middlewares, $middleware );
    }

    public function useExceptionManager(){
        $middleware = new ExceptionManager();
        $this->use($middleware);
    }

    public function useDataInputParser(){
        $middleware = new DataInputParser();
        $this->use($middleware);
    }


    public function execute(){
        //$this->middlewares[0]->setNext($this->middlewares[1])->setNext($this->middlewares[2])
        foreach ($this->middlewares as $index => $middleware) {
            if( isset( $this->middlewares[$index+1] ) ){
                $middleware->setNext( $this->middlewares[$index+1] );
            }
        } 

        $request = Request::getStandard();

        $this->middlewares[0]->handle( $request );
    }

}

