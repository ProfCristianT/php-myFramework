<?php 
namespace vendor\abstracts;
use vendor\classes\Request;

abstract class Middleware{
    protected $_next;//Aca guardare el siguiente eslabon de la cadena

    public function next(Request $request){
        if($this->_next){
            $this->_next->handle($request);
        }
    }

    public function setNext(Middleware $middleware):Middleware{
        return $this->_next = $middleware;
    }
}