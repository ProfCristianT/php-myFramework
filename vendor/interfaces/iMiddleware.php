<?php 
namespace vendor\interfaces;

use vendor\abstracts\Middleware;
use vendor\classes\Request;

interface iMiddleware{
    public function next(Request $request);

    public function setNext(Middleware $middleware):Middleware;

    public function handle(Request $request);
}