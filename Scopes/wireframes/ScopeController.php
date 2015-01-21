<?php
use Magic\Engine\Mvc\Controller;
class ScopeController extends Controller
{
	public function basicLayoutTasks(){
		$css = $this->scope->getThemeCss("default.css");
		$css->cor = "#f00";
		$this->html->addLink($css);
		$this->children = array("layout/sidebar");
	}
	public function render($content){
		$this->setViewPath("layout");
		$this->view->content = $content;
		$this->basicLayoutTasks();
		echo $this->html->render($this->getContent());

	}
}
?>