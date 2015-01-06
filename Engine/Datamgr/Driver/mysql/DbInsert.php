<?php 
namespace Magic\Engine\Datamgr\Driver\mysql;
use Magic\Engine\Datamgr\Sql\AbstractDbInsert;

class DbInsert extends AbstractDbInsert
{
    
    public function getQuery(){
        $columns = array_map(function($v){return "`".$v."`";},$this->getFields());
        $columns = "(".implode(",",$columns).")";
        $values = array_map(function($v){return ":$v";},$this->getFields());
        $values = "(".implode(",",$values).")";
        return "INSERT INTO `".$this->getTable()."` $columns VALUES $values";
    } # code...

}
?>