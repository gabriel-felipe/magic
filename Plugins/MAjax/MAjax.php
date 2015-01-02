<?php 
namespace Magic\Plugins\MAjax;
use Magic\Engine\Plugin\AbstractPlugin;
/**
* Majax - A common language for ajax between php, js and magic.
*/
class MAjax extends AbstractPlugin
{
	
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendJs();	
	}
	function getInstance(){
		return new MAjaxJson;
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