<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractElement;
use Magic\Engine\Form\ElementView;
use Magic\Engine\Validator\EmailValidator;
/**
* 		
*/
class EmailElement extends AbstractElement
{
	public $attrs = array("type"=>"text");

	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("input",$this));
		$this->addValidator(new EmailValidator());
	}
	
}

?>