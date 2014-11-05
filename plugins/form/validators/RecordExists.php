<?php 
/* Return valid if record already is at database */
class RecordExistsValidator implements IFormValidator
{
	protected $table,$column;
	protected $error = "";
	protected $errorString = "";
	protected $dbModel = null;
	function __construct($table,$column,$errorString=false)
	{
		$this->table = "`".$table."`";
		$this->column = "`".$column."`";
		if ($errorString) {
			$this->errorString = $errorString;
		} else {
			$this->errorString = "Value [v] not found on table {$this->table} at column {$this->column}.";
		}
		$this->dbModel = new dbmodel($this->table);
	}
	function getErrorMsg(){
		return $this->error;
	}
	function isValid($value){
		if ($this->doExist($value)) {
			$this->error = false;
			return true;
		} else {
			$this->error = str_replace("[v]",$value,$this->errorString);
			return false;
		}
	}
	function doExist($value){
		$res = (boolean)$this->dbModel->where("{$this->column} = :v",array("v"=>$value));
		return $res;
	}
}
?>