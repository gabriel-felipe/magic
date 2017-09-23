<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractMultiElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class SelectElement extends AbstractMultiElement
{
	protected $disabled=array();
	protected $selected=array();

	function setDisabled(array $array){
		$this->disabled = $array;
		return $this;
	}
	function getDisabled(){
		return $this->disabled;
	}
	function addDisabled($value){
		$this->disabled[] = $value;
		return $this;
	}

	function addSelected($value){
		$this->selected[] = $value;
		return $this;
	}

	function setSelected(array $array){
		$this->selected = $array;
		return $this;
	}
	function getSelected(){
		return $this->selected;
	}

	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("select",$this));
	}

	function isSelected($option){
		$elementValue = $this->getValue();
		if (is_array($elementValue)) {
			return in_array($option,$elementValue);
		} elseif(in_array($option,$this->selected)) {
			return true;
		} else {
			return (string)$elementValue === (string)$option;
		}
	}

	function isDisabled($option){
		return in_array($option,$this->selected);
	}
}

?>
