<?php 
/* Return valid if record already is at database */
class FileExistsValidator implements IFormValidator
{
	protected $path;
	protected $error = "";
	protected $errorString = "";
		function __construct($path=false,$errorString=false)
	{
		if(!$path){
			$path = path_root;
		}
		$this->path = $path;
		if ($errorString) {
			$this->errorString = $errorString;
		} else {
			$this->errorString = "File {$this->path}[v] not found.";
		}
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
		$res = (boolean)file_exists($this->path.$value);
		return $res;
	}
}
?>