<?php 
/**
* Classe para lidar com forms no backend. Validação / Limpeza de dados.
*/
class IForm
{
	protected $elements = array();
	protected $validateErrors = array();
	function addElement(IFormElement $element){
		$name = $element->getName();
		$this->elements[$name] = $element;
	}
	function populate(Array $data){
		foreach($data as $key => $value){
			if(array_key_exists($key, $this->elements)){
				$this->elements[$key]->setValue($value);
			}
		}
	}
	function addValidator($element,IFormValidator $validator){
		$this->elements[$element]->addValidator($validator);
	}
	function getValues(){
		$values = array();
		foreach($this->elements as $key => $element){
			$values[$key] = $element->getValue();
		}
		return $values;
	}
	function getRawValues(){
		$values = array();
		foreach($this->elements as $key => $element){
			$values[$key] = $element->getRawValue();
		}
		return $values;
	}
	function isValid(){
		$errors = array();
		foreach($this->elements as $key => $element){
			if (!$element->isValid()) {
				$errors[$key] = $element->getValidateErrors();
			}
		}
		$this->validateErrors = $errors;
		if (count($errors) > 0) {
			return false;
		} else {
			return true;
		}

	}
	function getValidateErrors(){
		return $this->validateErrors;
	}

}
?>