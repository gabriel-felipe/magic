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
	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("checkbox",$this));
	}
	protected $checked = false;
	public function getChecked(){
		return $this->checked;
	}
	public function setInputValue($value){
		$this->rawValue = $value;
		$this->sanitize();
	}

	public function setValue($value){
		if ($value == $this->rawValue) {
			$this->checked = true;
		} else {
			$this->checked = false;
		}
	}

	public function getValue(){
		if ($this->getChecked()) {
			return 0;
		} else {
			return $this->value;
		}
	}

}

?>