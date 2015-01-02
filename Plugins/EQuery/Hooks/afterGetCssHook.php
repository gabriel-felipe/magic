<?php 
namespace Magic\Plugins\EQuery\Hooks;
use Magic\Engine\Hooks\AbstractHook;
class afterGetCssHook extends AbstractHook
{
	public function action(array &$params=array()){
		$this->BottomScriptManager->equeries = $this->EQuery->getEqueriesFromJson(md5($this->LinkManager->getCachePath()));
	}
	public function register(){
		$this->LinkManager->registerHook($this,"afterGetAssets");
	}
}
?>