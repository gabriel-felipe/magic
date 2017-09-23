<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractMultiElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class SelectElement extends AbstractMultiElement
{
	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("select",$this));
	}
	
}

?>