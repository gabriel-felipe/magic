<?php 
/**
* Plugin para recarregar o css automaticamente ao salvar. Sem recarregar a página.
*/
class CssAutoLoadPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	public function init(){
		$this->registerHooks();	
	}
	public function appendJs($html){
		$html->addScript($this->getJs("CSSAutoLoad.js"));
	}
	public function getCss($html,$try=1){
		$json = new MAjax;

		if ($this->LinkManager->shouldRegenerateCache()) {
			$cssS = implode("\n",$html->getLinks());
			$version = time().mt_rand();
			$json->set("css",str_replace(".css",".css?version=".$version,$cssS));
			$json->setStatusCode(200);
			$json->render();
		} else {
			if ($try <= 20) {
				sleep(0.1);
				$this->getCss($html,$try+1);
			} else {
				$json->setStatusCode(304);
				$json->render();
			}
		}
	}
}
?>