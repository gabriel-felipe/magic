<?php 
namespace Magic\Engine\Datamgr\Sql;
use Magic\Engine\Datamgr\DbManager;
use \LogicException;
abstract class AbstractDbInsert extends AbstractDbQuery
{
    protected $data=array();

    public function __set($name,$value){
        $this->data[$name] = $value;
    }
    public function __get($name){
        return $this->data[$name];
    }
    public function getParams(){
        return $this->data;
    }
    public function checkFields(){
        $emptyKeys = array();
        foreach($this->getFields() as $f){
            if (!array_key_exists($f, $this->data)) {
                $emptyKeys[] = $f;
            }
        }
        if ($emptyKeys) {
            throw new LogicException("The following keys have no value: ".implode(", ",$emptyKeys).". Every setted key should receive a value, set it to null if don't want to send anything.", 1);                
        }
        
    }
    public function setData(array $data){
        $this->data = $data;
    }
    public function getData(){
        return $this->data;
    }
    public function run(){
        try {
            $this->checkFields();
            $params = $this->getParams();
            $query = $this->getQuery();
            $result = $this->db->query($query,$params);
            $this->registerHistory($query,$params);
            return $result[1];    
        } catch (Exception $e) {
            throw $e;            
        }
        
        
    }
}   
?>