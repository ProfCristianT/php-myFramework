<?php
namespace vendor\abstracts;

use vendor\dataSources\MySQLDataSource as DataSource;

abstract class Repository{
    // protected string $entity;
    // protected string $modelClassName;

    public function __construct(
        protected string $entity,
        protected string $modelClassName
    ){}

    public /*static*/ function find(array $match = null, array $project = null, array $sort = null){
    return (new DataSource())->find( $this->entity , $match, $project, $sort/*, $this->modelClassName*/);
        //REPARAR -> la instancia tenga los valores cargados
    }

    public /*static*/ function findOne(array $match = null, array $project = null, array $sort = null){
    return (new DataSource())->findOne( $this->entity , $match, $project, $sort/*, $this->modelClassName*/);
        //REPARAR -> la instancia tenga los valores cargados
    }

    public function create(array $values, bool $fromModel = false){
        if($fromModel){
            return (new DataSource())->create($this->entity, $values);//Retorna el id
        }
        // else{
        //     call_user_func_array([$this->modelClassName, "__construct"], $values);
        // }
        //TODO retornar una instancia del modelo
    }

    public function update(string|int|array $match, array|object $values){
        return (new DataSource())->update($this->entity, $match, $values);//Retorna un int con la cantidad de modificados
        //TODO retornar un bool / retornar el el objeto modificado
    }

    public function delete($match){
        return (new DataSource())->delete($this->entity, $match);//Retorna un int con la cantidad de borrados
        //TODO retornar un bool
    }
}