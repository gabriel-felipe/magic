<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class RadioElement extends TextElement
{
	public $attrs = array("type"=>"radio");
	protected $inputValue=null;
	protected $value=null;
	protected $rawValue=null;
	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("radio",$this));
	}
	protected $checked = false;

	public function getChecked(){
		return ($this->value == $this->inputValue and $this->value !== null);
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
			return null;
		}
	}

}

?>