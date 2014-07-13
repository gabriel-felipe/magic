<?php 
/**
* Interface para gerenciamento de elementos do form.
*/
abstract class IFormElement
{
	protected 
		$validators = array(), 
		$sanitizers = array(),
		$name = null,
		$value = null,
		$rawValue = null,
		$validateErrors = array();
	public function getName(){
		return $this->name;
	}
	public function __construct($name){
		$this->setName($name);
		return $this;
	}
	public function getType(){
		return $type;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function setValue($value){
		$this->rawValue = $value;
		$this->sanitize();
	}
	public function getValue(){
		return $this->value;
	}
	public function getRawValue(){
		return $this->rawValue;
	}
	public function addValidator(IFormValidator $validator){
		$this->validators[] = $validator;
		return $this;
	}
	public function addSanitizer(IFormSanitizer $sanitizer){
		$this->sanitizers[] = $sanitizer;
		$this->sanitize();
		return $this;
	}
	public function getValidateErrors(){
		return $this->validateErrors;
	}
	public function isValid(){
		$errors = array();
		foreach($this->validators as $validator){
			if ($validator->isValid($this->value)) {
				continue;
			} else {
				$errors[] = $validator->getErrorMsg();
			}
		}
		$this->validateErrors = $errors;
		if (count($errors) === 0) {
			return true;
		} else {
			return false;
		}
	}
	public function sanitize(){
		$value = $this->rawValue;
		foreach($this->sanitizers as $sanitize){
			$value = $sanitize->clean($value);
		}
		$this->value = $value;
	}
}
?>