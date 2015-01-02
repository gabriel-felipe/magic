<?php 
/**
* Majax - A common language for ajax between php, js and magic.
*/
class MAjaxPlugin extends AbstractPlugin
{
	
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		require_once("MAjax.php");
		$this->appendJs();	
	}
	function appendJs(){
		$js = $this->getJs("majax.js");
		$js->setPosition("top");
		$js->path_base = path_base;
		$js->scope = $this->scope->getName();
		$js->setPosition("top");
		$this->html->addScript($js);
	}
}
?>