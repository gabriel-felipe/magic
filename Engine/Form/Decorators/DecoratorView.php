<?php 
namespace Magic\Engine\Form\Decorators;
use Magic\Engine\Mvc\View\AbstractView;
final class DecoratorView extends AbstractView {
	protected $rootPath = "/Common/Templates/Form/Decorators/";
	public function __construct($path,AbstractFormDecorator $decorator){
		parent::__construct($path);
		$this->setDecorator($decorator);
		return $this;
	}
	public function setDecorator(AbstractFormDecorator $decorator){
		$this->decorator = $decorator;
		return $this;
	}
	public function getDecorator(){
		return $this->decorator;
	}
}
?>