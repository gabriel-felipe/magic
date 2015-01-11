<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Mvc\View\AbstractView;
final class FormView extends AbstractView {
	protected $rootPath = "/Common/templates/form/";
	public function __construct($path,Form $form){
		parent::__construct($path);
		$this->setForm($form);
		return $this;
	}
	public function setForm(form $form){
		$this->form = $form;
		return $this;
	}
	public function getForm(){
		return $this->form;
	}
}
?>