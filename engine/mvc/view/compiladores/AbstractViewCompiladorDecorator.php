<?php 
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