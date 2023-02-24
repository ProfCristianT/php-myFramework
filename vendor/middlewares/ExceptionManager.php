<?php 
namespace vendor\middlewares;

use vendor\abstracts\Middleware;
use vendor\interfaces\iMiddleware;
use vendor\classes\Request;
use Throwable;  

class ExceptionManager extends Middleware implements iMiddleware{
    public function handle(Request $request){
        $this->next($request);
        try{
        }
        catch(Throwable $e){
            var_dump($e);
            //Guardar errores en un archivo de logs 
            $p = fopen("../errores.log", "a");
            $exceptionLog =
                "Message: ".$e->getMessage().
                "\nFile: ".$e->getFile().
                "\nLine: ".$e->getLine().
                "\n-------------------------------------------------\n\n";
            fwrite($p, $exceptionLog);
            fclose($p);

            //Mostrar un HTML de error
        }
    }
}