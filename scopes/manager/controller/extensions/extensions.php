<?php 
/**
* 
*/
class ControllerExtensionsExtensions extends ManagerController
{
	public function after_construct(){
		require_once(path_root."/engine/extensionManager.php");
		$extManager = new extensionManager;
		$this->registry->set("extManager",$extManager);
	}
	function index(){
		
		$ext = $this->extManager->getExtension("adminWebingPro");
		print_r($this->extManager->install($ext,"manager"));
	}
}

?>