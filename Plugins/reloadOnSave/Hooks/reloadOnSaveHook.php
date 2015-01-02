<?php 
namespace Magic\Plugins\reloadOnSave\Hooks;
use Magic\Engine\Hooks\AbstractHook;
class reloadOnSaveHook extends AbstractHook
{
	public function action(array &$params=array()){
		if ($this->html->responseCode() == 200) {
			$this->reloadOnSave->setFiles(get_included_files());
			if (\data::post("checkReload")) {
				$this->reloadOnSave->reload();
			} else {
				$_SESSION['roSave'] = $this->reloadOnSave->getLastModDate();
				$this->reloadOnSave->appendJs();
			}
		}
	}
	public function register(){
		$this->hooks->registerHook($this,"before_html_render");
	}
	
	
}
?>