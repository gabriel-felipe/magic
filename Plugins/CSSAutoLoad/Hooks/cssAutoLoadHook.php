<?php 
namespace Magic\Plugins\CssAutoLoad\Hooks;
use Magic\Engine\Hooks\AbstractHook;
class cssAutoLoadHook extends AbstractHook
{
	protected $name = "cssAutoLoadHook";
	public function action(Array &$params){
		$html = $this->html;
		if ($this->html->responseCode() == 200) {
		
			if (isset($_POST['css_only'])) {
				ini_set('xdebug.max_nesting_level', 0);
				$this->CSSAutoLoad->getCss($html);
			} else {
				$this->CSSAutoLoad->appendJs($html);
			}
		}
	}
	public function register(){
		$this->hooks->registerHook($this,"before_html_render");
	}
}
?>