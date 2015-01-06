<?php 
namespace Magic\Engine\Datamgr\Sql;
use Magic\Engine\Datamgr\DbManager;
use \UnexpectedValueException;
abstract class AbstractDbDelete
{
    protected $db;  
    protected $table;
    protected $history=array();
    protected $where = array();
    function __construct(DbManager $db,$table)
    {
        $this->db = $db;
        $this->setTable($table);
    }

    abstract public function getQuery();
    public function getParams(){
        $params = array();
        foreach($this->getWhere() as $where){
            $params = array_merge($params,$where['params']);
        }
        return $params;
    }
    public function run(){
        $params = $this->getParams();
        $query = $this->getQuery();
        $result = $this->db->query($query,$params);
        $this->registerHistory($query,$params);
        return $result[1];
        
    }
    /**
     * Gets the value of db.
     *
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Sets the value of db.
     *
     * @param mixed $db the db
     *
     * @return self
     */
    public function setDb(DbManager $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Sets the value of table.
     *
     * @param mixed $table the table
     *
     * @return self
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Gets the value of table.
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    public function registerHistory($query,$params){
        $this->history = array("query"=>$query,"params"=>$params,"time"=>time());
        return $this;
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
}
?>
