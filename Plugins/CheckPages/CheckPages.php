<?php 
namespace Magic\Plugins\CheckPages;
use Magic\Engine\Plugin\AbstractPlugin;
/**
* Plugin para recarregar a página quando templates ou php's que interferem naquela página forem salvos
*/
class CheckPages extends AbstractPlugin
{
	protected $files;
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		ini_set('xdebug.max_nesting_level', 0);
		$this->registerHooks();
		$this->appendJs();
	}
	function appendJS(){
		$this->html->addScript($this->getJs("checkPages.js"));
	}
	function check($try=0){
		header("content-type: application/json");
		$json = $this->MAjax->getInstance();
		if ($this->pages !== $_SESSION['CheckPagesFiles']) {
			$json->setStatusCode(200);
			$_SESSION["CheckPagesFiles"] = $this->pages;
			$json->pages = $this->pages;
			$view = $this->scope->getView("layout/sidebar");
			$view->pages = $this->pages;
			$json->sidebar = $view->render();
			$json->render();
		} else {
			$json->setStatusCode(304);
			$json->render();
		}
	}
	
	function setPages($pages){
		$this->pages = $pages;
	}

	function getPages(){
		$files = glob($this->scope->getTemplateFolder()."/pages/*.tpl");
		$pages = array();
		foreach ($files as $file) {
			$pages[$file] = str_replace(".tpl","",basename($file));
		}
		return $pages;
	}
}

?>