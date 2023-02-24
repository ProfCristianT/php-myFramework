<?php

namespace vendor\dataSources;
use PDO;
use PDOException;
use PDOStatement;


function getConnectionInfo($connection){
    $connections = [
        "primary"=>[
            "dbname"=>"cursoPHP",
            "host"=>"127.0.0.1",
            "user"=>"root",
            "pass"=>"",
        ]
    ];

    return $connections[$connection];
}

class MySQLDataSource{
    private PDO $pdo;

    static private array $arrayPdo = [];

    public function __construct($connection = "primary"){
        if( isset( self::$arrayPdo[$connection] ) ){
            $this->pdo = self::$arrayPdo[$connection];
        }
        else{
            // global $connections;
            // $dbInfo = $connections[$connection];
            $dbInfo = getConnectionInfo($connection);
            $dsn = "mysql:dbname=$dbInfo[dbname];host=$dbInfo[host]";
    
            try {
                $pdo = new PDO($dsn, $dbInfo['user'], $dbInfo['pass']);
    
                self::$arrayPdo[$connection] = $pdo;
    
                $this->pdo = $pdo;
    
            } catch (PDOException $e) {
                echo 'Falló la conexión: ' . $e->getMessage();
            }
        }
    }


    public function find(string $table, array $match = null, array $project = null, array $sort = null, $class=null){
        $sql = $this->createSelectSQL($table, $match, $project, $sort);

        $stmt = $this->pdo->prepare($sql);

        if($match){
            $this->injectValues($stmt, $match);
        }
        try{
            $stmt->execute();
            if($class){
                //var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
                return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
            }
            else{
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        catch(PDOException $e){
            var_dump($e);
        }
    }

    public function findOne(string $table, array $match = null, array $project = null, array $sort = null, $class=null){
        $sql = $this->createSelectSQL($table, $match, $project, $sort, 1);

        $stmt = $this->pdo->prepare($sql);

        if($match){
            $this->injectValues($stmt, $match);
        }
        try{
            $stmt->execute();
            //$data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($class){
                $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
                return $stmt->fetch();
            }
            else{
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        catch(PDOException $e){
            var_dump($e);
        }
    }


    public function create(string $table, array $values){
        $fields = array_keys($values);//Crea un array usando los indices del array asociativo
        $fieldsList = $this->createFieldsListString($fields);
        $placesList = $this->createFieldsListString($fields, ":");

        $sql = "INSERT INTO $table ($fieldsList) value ($placesList)";

        $stmt = $this->pdo->prepare($sql);

        $this->injectValues($stmt, $values);

        try{
            $stmt->execute();
            return $this->pdo->lastInsertId();
        }
        catch(PDOException $e){
            var_dump($e);
        }
    }

    //TODO -> cambiar el array de match para que sea simple y no compuesto
    public function update(string $table, string|int|array $match, array $values){
        //createSetListString
        $setList = '';
        foreach ($values as $field => $value) {
            $setList .= " $field = :$field, ";
        }
        $setList = rtrim($setList, " ,");

        //createUpdateString
        //$sql = "UPDATE $table SET $setList WHERE $fieldSearch=:$fieldSearch ";
        $sql = "UPDATE $table SET $setList ";
        
        $this->createWhereString($sql, $match);  
        
        $stmt = $this->pdo->prepare($sql);
        $this->injectValues($stmt, $values);
        $this->injectValues($stmt, $match);
        //$stmt->bindParam(":$fieldSearch", $valueSearch);
        try{
            $stmt->execute();
            return $stmt->rowCount() ;
        }
        catch(PDOException $e){
            var_dump($e);
        }
    }


    
    public function delete($table, $match){
        $sql = "DELETE FROM $table ";
        $this->createWhereString($sql, $match);
       
        $stmt = $this->pdo->prepare($sql);
        $this->injectValues($stmt, $match);

        try{
            $stmt->execute();
            return $stmt->rowCount() ;
        }
        catch(PDOException $e){
            var_dump($e);
        }
    }

    private function getOperation(string $operation):string{
        switch( $operation ){
            case 'l': //Lower
                return '<';
            case 'le': //Lower - Equal
                return '<=';
            case 'g': //Greater
                return '>';
            case 'ge': //Greater - Equal
                return '>=';
            case 'e': //Equal
                return '=';
            case 'ne': //Not Equal
                return '<>';
        }
    }

    private function removeLastAnd(string &$sql){
        $pos = strripos($sql, "AND");
        
        if($pos !== false){
            //$sql = substr_replace($sql, "", $pos, strlen("AND"));
            $sql = substr_replace($sql, "", $pos, 3);
        }
    }

    private function createFieldsListString(array &$fields, string $before=""):string{
        $fieldsList = "";
        foreach ($fields as $field) {
            $fieldsList .= " $before$field, ";
        }
        $fieldsList = rtrim( $fieldsList , " ,");
        return $fieldsList;
    }

    private function createSelectSQL(string &$table, array &$match = null, array &$project = null, 
                                    array $sort = null, int $limit = null, int $offset = null):string{
        $projectString='';

        if($project){
            $projectString = $this->createFieldsListString($project);
        }
        else{
            $projectString = "*";
        }    

        $sql = "SELECT $projectString FROM $table";

        $this->createWhereString($sql, $match);
        
        $this->createLimitString($sql, $limit, $offset);

        return $sql;
    }

    private function createWhereString(string &$sql, array &$match = null){
        if( $match ){
            $sql .= ' WHERE ';

            foreach ($match as $field => $value) {
                if( is_string($value) || is_numeric($value) ){
                    $sql .= " $field = :$field ";
                }
                else if(is_array($value)){
                    $op = $this->getOperation($value['operation']);
                    $sql .= " $field $op :$field ";
                }
                $sql .= ' AND ';
            }
            
            $this->removeLastAnd($sql);
        }
    }

    private function createLimitString(string &$sql, int &$limit = null, int &$offset = null){
        if($limit && !$offset){
            $sql .= " LIMIT $limit ";
        }
        else if($limit && $offset){
            $sql .= " LIMIT $offset, $limit ";
        }
    }

    private function injectValues(PDOStatement &$stmt, array &$values){
        foreach ($values as $field => $value) {
            if( is_string($value) || is_numeric($value) || is_null($value) || is_bool($value)){
                //$stmt->bindValue(":category", $values['category']);
                $stmt->bindValue(":$field", strip_tags( $value ) );
            }
            else if( is_array($value) ){
                $stmt->bindValue(":$field", strip_tags( $value['value'] ) );
            }
        }
    }
}
