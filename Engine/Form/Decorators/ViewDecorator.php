<?php 
namespace Magic\Engine\Form\Decorators;
use Magic\Engine\Mvc\View\AbstractView;

class ViewDecorator extends AbstractFormDecorator
{
	protected $viewDecorada;
	function __construct(AbstractView $view){
		$this->viewDecorada = $view;

	}
	function compilar($conteudo){
		$this->viewDecorada->element = $this->getElement();
		$this->viewDecorada->conteudo = $conteudo;
		return $this->viewDecorada->render();
	}
	function __clone(){
		$this->viewDecorada = clone $this->viewDecorada;
	}
}
?>