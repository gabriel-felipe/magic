<?php 
namespace Magic\Engine\Datamgr\Sql;
use Magic\Engine\Datamgr\DbManager;
use \UnexpectedValueException;
abstract class AbstractDbQuery
{
	protected $db;	
    protected $fields=array();
    protected $table;
    protected $history=array();
	function __construct(DbManager $db,$table,$fields="*")
	{
		$this->db = $db;
		$this->setTable($table);
		$this->setFields($fields);
	}

	abstract public function getQuery();
    abstract public function getParams();
    abstract public function run();

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
     * Sets the value of fields.
     *
     * @param mixed $fields the fields
     *
     * @return self
     */
    public function setFields($fields)
    {
        if ($fields=="*") {
            $fields = $this->db->fetchColumns($this->getTable());
            $fields = array_map(function($v){return $v['name'];},$fields);
        } elseif (is_string($fields)) {
            $fields = explode(",",$fields);
        }
        if (is_array($fields)) {
            $this->fields = $fields;
        } else {
            throw new UnexpectedValueException("Error Processing Request", 1);  
        }
        return $this;
    }
    /**
     * Gets the value of fields.
     *
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function removeField($field){
        $key = array_search($field, $this->fields);
        if ($key !== false) {
            unset($this->fields[$key]);
        }
    }

    public function addField($field){
        $this->fields[] = $field;
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

}
?>
