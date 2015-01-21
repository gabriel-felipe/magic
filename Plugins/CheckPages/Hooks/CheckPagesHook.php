<?php 
namespace Magic\Plugins\CheckPages\Hooks;
use Magic\Engine\Hooks\AbstractHook;
class CheckPagesHook extends AbstractHook
{
	public function action(array &$params=array()){
		if ($this->html->responseCode() == 200) {
			$this->CheckPages->setPages($this->CheckPages->getPages());
			if (\data::post("CheckPages")) {
				$this->CheckPages->check();
			} else {
				$_SESSION['CheckPagesFiles'] = $this->CheckPages->getPages();
				$this->CheckPages->appendJs();
			}
		}
	}
	public function register(){
		$this->hooks->registerHook($this,"before_html_render");
	}
	
	
}
?>