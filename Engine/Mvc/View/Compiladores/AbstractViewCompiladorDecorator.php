<?php 
	namespace Magic\Engine\Mvc\View\Compiladores;
	use Magic\Engine\Compilador\AbstractCompiladorDecorator;
	use Magic\Engine\Mvc\View\AbstractView;

	/**
	* Decorator para compilar assets
	*/
	abstract class AbstractViewCompiladorDecorator extends AbstractCompiladorDecorator implements InterfaceViewCompilador
	{
		protected $view;
		public function setView(AbstractView $view){
			$this->view = $view;
		}
		public function getView(){
			return $this->view;
		}
	}
?>