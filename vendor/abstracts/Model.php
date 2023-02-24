<?php 

namespace vendor\abstracts;

abstract class Model{

    public function __construct(
        private bool $isNew,
        private string $identifier,
        private object $repository
    ){}
    
    public function convertToArray(){
        $array = [];
        foreach ($this as $index => $value) {
            if( $index === "isNew" || 
                $index === "identifier" || 
                $index === "repository") 
            {continue;}
            
            $array[$index] = $value;
        }
        return $array;
    }

    public function save(){
        //isNew es true -> crear en BBDD
        $array = $this->convertToArray();
        
        
        if( $this->isNew ){
            $this->{$this->identifier} = ($this->repository)->create( $array , true);
            $this->isNew = false;
        }
        //isNew es false -> actualizar la BBDD
        else{
            ($this->repository)->update( 
                            [ $this->identifier   =>   $this->{$this->identifier}] , 
                            $array,
                            true 
                        );
        }
    }

    public function delete(){
        if(!$this->isNew){
            ($this->repository)->delete( 
                [ $this->identifier   =>   $this->{$this->identifier}]
            );
        }
    }
}