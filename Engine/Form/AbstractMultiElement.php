<?php 
namespace Magic\Engine\Form;
abstract class AbstractMultiElement extends AbstractElement {
	protected $options=array();
	protected $strictOptionCompare = false;
	public function setOptions(array $options){
		$this->options = $options;
		return $this;
	}
	public function addOption($value,$alias=false){
		$alias = (!$alias) ? $value : $alias;
		$this->options[$value] = $alias;
		return $this;
	}
	public function prependOption($value,$alias=false){
		$result = array($value=>$alias);
		$this->options = $result + $this->options;
		return $this;
	}
	public function getOptions(){
		return $this->options;
	}

	public function checkValue($value){
		if (is_array($this->value)) {
			return in_array($value, $this->value,$this->strictOptionCompare);
		} else{
			return $value == $this->value;
		}
	}
}
?>