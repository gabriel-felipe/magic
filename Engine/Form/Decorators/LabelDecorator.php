<?php 
namespace Magic\Engine\Form\Decorators;
class LabelDecorator extends AbstractFormDecorator
{
	protected $before=false;
	public function __construct($before=false){
		$this->before = $before;
	}
	function compilar($value){

		$element = $this->getElement();
		$label = "<label for='".$element->getId()."'>".$element->getLabel()."</label>";
		if (is_a($element,"Magic\Engine\Form\Elements\HiddenElement")) {
			return $value;
		}
		if ($this->before) {
			return $label.$value;
		} else {
			return $value.$label;
		}

	}
}
?>