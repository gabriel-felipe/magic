<?php 
class EmailValidator implements IFormValidator
{
	protected $error = "";
	protected $errorString = "";
	function __construct($errorString = "Value should be a valid email"){
		$this->errorString = $errorString;
	}
	function getErrorMsg(){

		return $this->error;
	}
	function isValid($value){
		if (validate::email($value)) {
			return true;
		} else {
			$this->error = $this->errorString;
			return false;
		}
	}
}
?>