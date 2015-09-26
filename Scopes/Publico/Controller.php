<?php
namespace Publico;
use Magic\Engine\Mvc\Controller as MgcController;
class Controller extends MgcController
{
	public function basicLayoutTasks(){
		$css = $this->scope->getThemeCss("default.css");
		$css->cor = "#f00";
		$this->html->addLink($css);
		$this->children = array("Layout/Header","Layout/Footer");
	}
	public function render($content){
		$this->setViewPath("Layout");
		$this->view->content = $content;
		$this->basicLayoutTasks();
		echo $this->html->render($this->getContent());

	}
}
?>