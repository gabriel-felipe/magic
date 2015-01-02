<?php
use Magic\Engine\Mvc\Controller;
class publicController extends Controller
{
	public function basicLayoutTasks(){
		$this->html->addLink($this->scope->getThemeCss("default.css"));
		$this->children = array("layout/header","layout/footer");
	}
	public function render($content){
		$this->setViewPath("layout");
		$this->view->content = $content;
		$this->basicLayoutTasks();
		echo $this->html->render($this->getContent());

	}
}
?>