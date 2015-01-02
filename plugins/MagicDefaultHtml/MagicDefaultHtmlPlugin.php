<?php 
/**
* Plugin responsável por inserir os assets padrões do Magic ao documento.
*/
class MagicDefaultHtmlPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendCss();
		$this->appendJs();
		$this->appendMetas();
	}
	function appendJs(){
		$js = $this->getJs("jquery-1.9.1.js");
		$js->setPosition("top");
		$this->html->addScript($js);
	}
	function appendCss(){
		$css = $this->getCss("magic.css");
		$cssData = $this->config->get("magicCss");
		foreach ($cssData as $key => $value) {
			$css->{$key} = $value;
		}
		$this->html->addLink($css);
	}
	function appendMetas(){
		$this->html->addMeta(new Meta("contentType","http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\""));
		$this->html->addMeta(new Meta("viewport","name=\"viewport\" content=\"width=device-width,initial-scale=1\""));
	}

}
?>