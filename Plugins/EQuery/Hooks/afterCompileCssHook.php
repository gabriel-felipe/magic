<?php 
namespace Magic\Plugins\EQuery\Hooks;
use Magic\Engine\Hooks\AbstractHook;
class afterCompileCssHook extends AbstractHook
{
	public function action(array &$params=array()){
		$this->EQuery->writeEqueriesToJson(md5($this->LinkManager->getCachePath()));
	}
	public function register(){
		$this->LinkManager->registerHook($this,"afterCompileAssets");
	}
}
?>