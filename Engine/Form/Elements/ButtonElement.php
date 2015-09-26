<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractMultiElement;
use Magic\Engine\Form\ElementView;

/**
* 		
*/
class ButtonElement extends AbstractMultiElement
{
	protected $body;
	public function setBody($body){
		$this->body = $body;
	}
	public function getBody(){
		return ($this->body) ? $this->body : ucfirst($this->getName());
	}
	function setUp(){
		global $registry;
		$this->view = $registry->ViewHandler->prepare(new ElementView("button",$this));
	}
	
}

?>