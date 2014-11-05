<?php 
/* Return valid if record is not found at database */
class NoRecordExistsValidator implements IFormValidator
{
	protected $table,$column;
	protected $error = "";
	protected $errorString = "";
	protected $primaryKey = false;
	function __construct($table,$column,$errorString=false,$pk=false)
	{
		$this->primaryKey = $pk;
		$this->table = $table;
		$this->column = $column;
		if ($errorString) {
			$this->errorString = $errorString;
		} else {
			$this->errorString = "Value already found on table {$this->table} at column {$this->column}.";
		}
	}
	function getErrorMsg(){
		return $this->error;
	}
	function isValid($value){
		if ($this->doExist($value)) {
			$this->error = $this->errorString;
			return false;
		} else {
			$this->error = false;
			return true;
		}
	}
	function doExist($value){
		$dbModel = new dbmodel($this->table);
		if (!$this->primaryKey) {
			return (boolean)$dbModel->where("{$this->column} = :v",array("v"=>$value));
		} else {
			return (boolean)$dbModel->where("{$this->column} = :v and {$this->table}_id <> :pk",array("v"=>$value,"pk"=>$this->primaryKey));
		}
		
	}
}
?>