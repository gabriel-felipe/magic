<?php 
class StringValidator implements IFormValidator
{
	protected $min,$max;
	protected $error = "";
	protected $errorString = "";
	function __construct($min=1,$max=9999999,$errorString=false)
	{
		$this->min = $min;
		$this->max = $max;
		if ($errorString) {
			$this->errorString = $errorString;
		} else {
			$this->errorString = "Value should fit the interval between {$this->min} and {$this->max} characters.";
		}
	}
	function getErrorMsg(){
		return $this->error;
	}
	function isValid($value){
		$len = strlen($value);
		if ($len >= $this->min and $len <= $this->max) {
			$this->error = false;
			return true;
		} else {
			$this->error = $this->errorString;
			return false;
		}
	}
}
?>