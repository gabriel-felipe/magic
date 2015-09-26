<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;

/**
* Classe para lidar com forms no backend. Validação / Limpeza de dados.
*/
class DisplayGroup extends Form
{
	protected $parentForm=false;
	public function getNewView(){
		return new FormView("displayGroup",$this);
	}
	public function setParentForm(Form $form){
		$this->parentForm = $form;
	}
	public function addElemntsToParent(){
		if ($this->parentForm) {
			$elements = array_merge($this->parentsForm->getElements(),$this->getElements());
			$this->parentForm->setElements($elements);
		}
	}
	public function addElement(AbstractElement $element,$prepareElement=true,$addToParent=true){
		if ($prepareElement) {
			$this->prepare($element);
		}
		
		$name = $element->getId();
		$this->elements[$name] = $element;
		if ($this->parentForm) {
			$this->parentForm->addElement($element,$prepareElement);
		}
	}
}
?>