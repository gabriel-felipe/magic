<?php 
namespace Magic\Engine\Validator;
abstract class AbstractValidator {
	protected $errorMsg;
	protected $error=false;
	public function setErrorMsg($errorMsg){
		$this->errorMsg = $errorMsg;
		return $this;
	}
	public function getErrorMsg(){
		return $this->errorMsg;
	}

	abstract function getErrorParams($value);

	public function getError(){
		return $this->error;
	}

	public function getParsedError($params){
		$msg = $this->getErrorMsg();
		return strtr($msg,$params);
	}

	abstract function validate($value);


	public function isValid($value){
		$this->error = false;
		if ($this->validate($value)) {
			return true;
		} else {
			$this->error = $this->getParsedError($this->getErrorParams($value));
			return false;
		}
	}
}
?>