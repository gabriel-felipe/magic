<?php 
namespace Magic\Engine\Datamgr\Driver\mysql;
use Magic\Engine\Datamgr\Sql\AbstractDbUpdate;
class DbUpdate extends AbstractDbUpdate
{
    
    public function getQuery(){
    	$data = "";
        $values = array_map(function($v){return "`".$v."` = :$v";},$this->getFields());
        $values = implode(",",$values);

        $where = $this->getWhere();
        $whereQuery = "";
        if ($where) {

            $whereQuery = " WHERE ";
            $c = 0;
            foreach ($where as $info) {
                $whereQuery .= ($c > 0) ?  " ".$info['glue'] : "";
                $whereQuery .= " (".$info['query'].") ";
                $c++;
            }
        }

        $limitQuery = "";
        $limit = $this->getLimit();
        if ($limit) {
        	$limitQuery = " LIMIT ".$limit;
        }

        $order = $this->getOrderBy();
        $orderBy = "";
        if ($order) {
            $orderBy = " ORDER BY ";
            foreach ($order as $info) {
                $orderBy .= $info['column']." ".$info['mode']." ";
            }
            
        }

        return "UPDATE `".$this->getTable()."` SET $values $whereQuery $orderBy $limitQuery";
    } # code...

}
?>