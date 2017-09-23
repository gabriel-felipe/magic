<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Mvc\View\AbstractView;
final class ElementView extends AbstractView {
	protected $rootPath = "/Common/templates/form/";
	public function __construct($path,AbstractElement $element){
		parent::__construct($path);
		$this->setElement($element);
		return $this;
	}
	public function setElement(AbstractElement $element){
		$this->element = $element;
		return $this;
	}
	public function getElement(){
		return $this->element;
	}
}
?>