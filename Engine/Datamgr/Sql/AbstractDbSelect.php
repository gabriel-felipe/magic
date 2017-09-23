<?php 
namespace Magic\Engine\Datamgr\Sql;
use Magic\Engine\Datamgr\DbManager;
use \UnexpectedValueException;
abstract class AbstractDbSelect extends AbstractDbQuery
{
	protected $where=array();
    protected $having=array();
	protected $joins=array();
	protected $table;
	protected $customFields = array();
	protected $history=array();
	protected $qtnByPage=99999999999999;
	protected $orderBy=array();
	protected $page=false;
	protected $groupBy = array();
	
    public function run(){
        $params = $this->getParams();
        $query = $this->getQuery();
        $result = $this->db->query($query,$params);
        $this->registerHistory($query,$params);
        return $result;
        
    }

    public function getTable(){
        return $this->table;
    }

    abstract public function setRandOrder();
    public function getParams(){
        $params = array();
        foreach($this->getWhere() as $where){
            $params = array_merge($params,$where['params']);
        }
        foreach($this->getHaving() as $having){
            $params = array_merge($params,$having['params']);
        }
        foreach($this->getCustomFields() as $field){
            $params = array_merge($params,$field['params']);
        }
        foreach($this->getJoins() as $join){
            $params = array_merge($params,$join['params']);
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

    /**
     * Gets the value of having.
     *
     * @return mixed
     */
    public function getHaving()
    {
        return $this->having;
    }

    /**
     * Sets the value of having.
     *
     * @param mixed $having the having
     *
     * @return self
     */
    public function setHaving(array $having)
    {
        $this->having = $having;

        return $this;
    }

    public function addHaving($query,$params=array(),$glue="and"){
        $this->having[] = array(
            "query"=>$query,
            "params"=>$params,
            "glue"=>$glue);
    }

    /**
     * Gets the value of joins.
     *
     * @return mixed
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * Sets the value of joins.
     *
     * @param mixed $joins the joins
     *
     * @return self
     */
    public function setJoins(array $joins)
    {
        $this->joins = $joins;

        return $this;
    }



    /**
     * Gets the value of customFields.
     *
     * @return mixed
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }

    /**
     * Sets the value of customFields.
     *
     * @param mixed $customFields the add fields
     *
     * @return self
     */
    public function setCustomFields(array $customFields)
    {
        $this->customFields = $customFields;

        return $this;
    }

    public function addCustomField($query,$name=false,$params=array())
    {
        $this->customFields[] = array("query"=>$query,"name"=>$name,"params"=>$params);
        return $this;
    }

    /**
     * Gets the value of hooks.
     *
     * @return mixed
     */
    public function getHooks()
    {
        return $this->hooks;
    }

   	

    /**
     * Gets the value of history.
     *
     * @return mixed
     */
    public function getHistory()
    {
        return $this->history;
    }

    

	public function addJoin($table,$fields=array(),$on="",$method='left',$params=array()){
		$on = ($on) ? $on : "`".$table."`".".".$table."_id = `".$this->getTable()."`.".$table."_id";
		$this->joins[$table] = array(
			"fields" => $fields,
			"on" => $on,
			'method'=>$method,
            "params"=>$params
		);
	}
	public function removeJoin($table){
		unset($this->joins[$table]);
	}
	public function removeCustomField($alias){
		unset($this->customFields[$alias]);
	}    

    /**
     * Gets the value of groupBy.
     *
     * @return mixed
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }



    /**
     * Sets the value of groupBy.
     *
     * @param mixed $groupBy the group by
     *
     * @return self
     */
    public function setGroupBy(array $groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    public function addGroupBy($groupBy)
    {
        $this->groupBy[] = $groupBy;

        return $this;
    }

    /**
     * Gets the value of groupBy.
     *
     * @return mixed
     */
    

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

    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setQtnByPage($qtnByPage)
    {
        $this->qtnByPage = $qtnByPage;
        return $this;
    }

    public function getQtnByPage()
    {
        return $this->qtnByPage;
    }
}
?>
