<?php 
namespace Magic\Engine\Form\Decorators;
use Magic\Engine\Mvc\View\Compiladores\AbstractViewCompiladorDecorator;
use Magic\Engine\Form\AbstractElement;

abstract class AbstractFormDecorator extends AbstractViewCompiladorDecorator
{
	protected $element;
	public function setElement(AbstractElement $element){
		$this->element = $element;
		return $this;
	}
	public function getElement(){
		return $this->element;
	}
}
?>