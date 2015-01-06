<?php 
namespace Magic\Engine\Datamgr\Sql;
use Magic\Engine\Datamgr\DbManager;
use \UnexpectedValueException;
abstract class AbstractDbUpdate extends AbstractDbInsert
{
	protected $where=array();
    protected $limit=false;
	protected $orderBy = array();
	
    public function getParams(){
        $params = $this->getData();
        foreach($this->getWhere() as $where){
            $params = array_merge($params,$where['params']);
        }
        return $params;
    }

    /**
     * Gets the value of where.
     *
     * @return mixed
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * Sets the value of where.
     *
     * @param mixed $where the where
     *
     * @return self
     */
    public function setWhere(array $where)
    {
        $this->where = $where;

        return $this;
    }

    public function addWhere($query,$params=array(),$glue="and"){
        $this->where[] = array(
            "query"=>$query,
            "params"=>$params,
            "glue"=>$glue);
    }

   

    public function getOrderBy()
    {
        return $this->orderBy;
    }



    /**
     * Sets the value of orderBy.
     *
     * @param mixed $orderBy the group by
     *
     * @return self
     */
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function addOrderBy($orderBy,$mode="")
    {   
        if ($mode !== "ASC" and $mode !== "DESC" and $mode !== "") {
            throw new UnexpectedValueException("Order Mode ($mode) does not exist, please use ASC or DESC", 1);
            
        }
        $this->orderBy[] = array(
            "column"=>$orderBy,
            "mode"=>$mode
        );

        return $this;
    }

    public function reverseOrder(){
        $order = $this->getOrderBy();
        $order = array_reverse($order);
        foreach($order as $key => $value){

            if ($value['mode'] == "ASC") {
                $order[$key]['mode'] = "DESC";
            } else {
                $order[$key]['mode'] = "ASC";
            }
        }
        $this->setOrderBy($order);
    }

    public function getLimit(){
        return $this->limit;
    }

    public function setLimit($limit){
        $this->limit = $limit; 
    }
}
?>
