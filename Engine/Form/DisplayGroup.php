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
	protected $cascadePrepare=false;
	public function getNewView(){
		$view = new FormView("displayGroup");
		$view->form = $this;
		return $view;
	}
	public function setParentForm(Form $form){
		$this->parentForm = $form;
	}
	public function addElementsToParent(){
		if ($this->parentForm) {
			$elements = array_merge($this->parentsForm->getElements(),$this->getElements());
			$this->parentForm->setElements($elements);
		}
	}
	public function setCascadePrepare($cascade){
		$this->cascadePrepare = $cascade;
		return $this;
	}
	public function addElement(AbstractElement $element,$prepareElement=true,$addToParent=true,$cascadePrepare=false){
		if ($prepareElement) {
			$this->prepare($element);
		}
		if (!$cascadePrepare) {
			$cascadePrepare = $this->cascadePrepare;
		}
		
		$name = $element->getId();
		$this->elements[$name] = $element;
		if ($this->parentForm and $addToParent) {
			$this->parentForm->addElement($element,$cascadePrepare,$addToParent,$cascadePrepare);
		}
	}
}
?>