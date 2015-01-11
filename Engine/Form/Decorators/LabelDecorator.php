<?php 
namespace Magic\Engine\Form\Decorators;
class LabelDecorator extends AbstractFormDecorator
{
	function compilar($value){

		$element = $this->getElement();
		if (is_a($element,"Magic\Engine\Form\Elements\HiddenElement")) {
			return $value;
		}
		return "<label for='".$element->getId()."'>".$element->getLabel()."</label>".$value;

	}
}
?>