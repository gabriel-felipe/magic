<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractMultiElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class MultiCheckboxElement extends AbstractMultiElement
{

	function setUp(){
		global $registry;
		$this->setAttr("type","checkbox");
		$this->view = $registry->ViewHandler->prepare(new ElementView("multi-checkbox",$this));
	}
	protected $checked = false;
	public function getChecked(){
		return $this->checked;
	}
	public function setInputValue($value){
		$this->rawValue = $value;
		$this->sanitize();
	}
}

?>