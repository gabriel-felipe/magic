<?php 
namespace Magic\Engine\Datamgr\Driver\mysql;
use Magic\Engine\Datamgr\Sql\AbstractDbDelete;

class DbDelete extends AbstractDbDelete
{
    
    public function getQuery(){
    	

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

        return "DELETE FROM `".$this->getTable()."` $whereQuery";
    } # code...

}
?>