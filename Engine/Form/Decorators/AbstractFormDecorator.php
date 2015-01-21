<?php 
namespace Magic\Engine\Form\Decorators;
use Magic\Engine\Mvc\View\Compiladores\AbstractViewCompiladorDecorator;
use Magic\Engine\Form\AbstractElement;
use Magic\Engine\Form\Form;

abstract class AbstractFormDecorator extends AbstractViewCompiladorDecorator
{
	protected $element;
	public function setElement($element){
		if (!is_a($element, "Magic\Engine\Form\AbstractElement") and !is_a($element, "Magic\Engine\Form\Form")) {
			throw new Exception("Element must be of type AbstractElement or Form", 1);	
		}
		$this->element = $element;
		return $this;
	}
	public function getElement(){
		return $this->element;
	}
}
?>