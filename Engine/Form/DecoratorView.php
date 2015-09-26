<?php 
namespace Magic\Engine\Form\Decorators;
use Magic\Engine\Mvc\View\AbstractView;
class DecoratorView extends AbstractView {
	protected $rootPath = "/Common/Templates/Form/Decorators/";
	protected $decorator;
	public function __construct($path,Form $decorator){
		parent::__construct($path);
		$this->setDecorator($decorator);
		return $this;
	}
	public function setDecorator(form $decorator){
		$this->decorator = $decorator;
		return $this;
	}
	public function getForm(){
		return $this->decorator;
	}
}
?>