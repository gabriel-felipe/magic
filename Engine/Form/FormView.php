<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Mvc\View\AbstractView;
final class FormView extends AbstractView {
	protected $rootPath = "/Common/templates/form/";
	public function __construct($path){
		parent::__construct($path);
		return $this;
	}
}
?>