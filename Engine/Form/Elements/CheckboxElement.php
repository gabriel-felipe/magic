<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class CheckboxElement extends TextElement
{
	public $attrs = array("type"=>"checkbox");
	protected $inputValue=0;
	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("checkbox",$this));
	}
	protected $checked = false;

	public function getChecked(){
		return $this->value == $this->inputValue;
	}
	public function setInputValue($value){
		$this->inputValue = $value;
	}
	public function getInputValue(){
		return $this->inputValue;
	}

	public function getValue(){
		if ($this->getChecked()) {
			return $this->value;
		} else {
			return 0;
		}
	}

}

?>