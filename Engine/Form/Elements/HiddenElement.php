<?php 
namespace Magic\Engine\Form\Elements;
use Magic\Engine\Form\AbstractElement;
use Magic\Engine\Form\ElementView;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;


/**
* 		
*/
class HiddenElement extends TextElement
{
	public $attrs = array("type"=>"hidden");

	public function addDecorator(AbstractFormDecorator $decorator,$name=false){
		return false;
	}
}

?>