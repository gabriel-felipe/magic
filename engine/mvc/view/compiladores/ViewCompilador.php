<?php 
/**
* Compilador de Views
*/
class ViewCompilador extends Compilador implements InterfaceViewCompilador
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