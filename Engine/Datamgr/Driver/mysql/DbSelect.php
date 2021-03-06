<?php 
namespace Magic\Engine\Datamgr\Driver\mysql;
use Magic\Engine\Datamgr\Sql\AbstractDbSelect;

class DbSelect extends AbstractDbSelect
{
    
    public function getQuery(){
        $keys = $this->getFields();
        foreach($keys as &$key){
            if (count(explode(".",$key)) >= 2) {
                continue;
            }
            $key = $this->getTable().".$key";
        }
        $keys = implode(", ",$keys);
        $from = " FROM `".$this->getTable()."` ";
        $joins = "";
        foreach ($this->joins as $table => $join) {
            $on = str_replace("[t]",$this->getTable(), $join['on']);
            $joins .= $join['method']." JOIN `$table` on ($on) ";
            foreach($join['fields'] as $field => $name){
                $keys .= ", $table.$field as `$name` ";
            }
        }
        

        foreach($this->customFields as $info){
            $field = $info['name'];
            $query = $info['query'];
            $query = str_replace("[t]",$this->getTable(), $query);
            $keys .= ", $query ";
            if ($field) {
                $keys .= "as `$field` ";
            }
        }
        $keys = trim($keys);
        $keys = trim($keys,",");

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

        $group = $this->getGroupBy();
        $groupBy = "";
        if ($group) {
            $groupBy = " GROUP BY ".implode(", ",$group)." ";
        }

        $having = $this->getHaving();
        $havingQuery = "";
        if ($having) {

            $havingQuery = " HAVING ";
            $c = 0;
            foreach ($having as $info) {
                $havingQuery .= ($c > 0) ?  " ".$info['glue'] : "";
                $havingQuery .= " (".$info['query'].") ";
                $c++;
            }
        }

        $order = $this->getOrderBy();
        $orderBy = "";
        if ($order) {
            $orderBy = " ORDER BY ";
            $o = 0;
            foreach ($order as $info) {
                if ($o > 0) {
                    $orderBy .= ", ";
                }
                $orderBy .= $info['column']." ".$info['mode']." ";
                $o++;
            }
            
        }

        $limit = "";
        $page = $this->getPage();
        $qtnByPage = $this->getQtnByPage();
        if ($page and $qtnByPage) {
            $limit = " LIMIT ";
            if ($page) {
                $limit .= (($page - 1)*$qtnByPage).", ";
            }
            $limit .= $qtnByPage." ";
        } else if(($page and !$qtnByPage) or ($qtnByPage and !$page)){
            throw new \LogicException("Set both DbSelect::page and DbSelect::qtnByPage or none of them", 1);
            
        }

        
        return "SELECT ".$keys.$from.$joins.$whereQuery.$groupBy.$havingQuery.$orderBy.$limit;

    } # code...

    public function setRandOrder(){
        $this->setOrderBy(array());
        $this->addOrderBy("RAND()");
    }
}
?>