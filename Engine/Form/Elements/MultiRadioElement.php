<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractMultiElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class MultiRadioElement extends AbstractMultiElement
{
	protected $checked = false;

	function setUp(){
		global $registry;
		$this->setAttr("type","radio");
		$this->view = $registry->ViewHandler->prepare(new ElementView("multi-radio",$this));
	}
	public function getChecked(){
		return $this->checked;
	}
	public function setInputValue($value){
		$this->rawValue = $value;
		$this->sanitize();
	}
}

?>