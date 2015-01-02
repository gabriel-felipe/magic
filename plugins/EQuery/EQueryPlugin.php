<?php 
/**
* EQueryPlugin
*/
class EQueryPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	protected $equeries = array();
	protected $cacheFile = "equery.cache.json";
	function init()
	{
		$this->appendCompiladorCss();
		$this->appendJs();
		$this->registerHooks();
	}
	function addEquery($element,$rule){
		$this->equeries[] = array(
			"element"=>$element,
			"rule" => $rule
		);
	}
	function getEqueries(){
		return $this->equeries;
	}
	function writeEqueriesToJson($alias){
		$filePath = $this->getFolder()."/".$this->cacheFile;
		$file = json_decode(file_get_contents($filePath),1);
		$file[$alias] = $this->getEqueries();
		$fhandler = fopen($filePath,"w+");
		fwrite($fhandler, json_encode($file));
		fclose($fhandler);
	}
	function getEqueriesFromJson($alias){
		$filePath = $this->getFolder()."/".$this->cacheFile;
		$file = json_decode(file_get_contents($filePath),1);
		return (isset($file[$alias])) ? $file[$alias] : array();
	}
	function appendCompiladorCss(){
		require_once("EQueryCssCompiladorDecorator.php");
		$this->LinkManager->addCompiladorUnidadeDecorator(new EQueryCssCompiladorDecorator());
	}
	function appendJs(){
		$js = $this->getJs("EQuery.js");
		$this->html->addScript($js);
	}
}
// public function get_js_inline(){
// 			$arquivo = $this->path_cache."/equery.js";
// 			$equery = file_get_contents($arquivo);
// 			$equery = json_decode($equery);
// 			if(!is_array($equery))
// 				$equery = array();
// 			foreach($equery as $query){
// 				$this->add_js_inline(
// 					"$(\"{$query[0]}\").equery(\"$query[1]\");");
// 			}
// 			$html = '
// <script name="js-inline">
// 			$(document).ready(function(){
// 			';
// 			foreach($this->js_inline as $name=>$script){
// 				$html .= <<<JSINLINE
// 					$script
				
// JSINLINE;
// 			}
// 			$html .="
// 		});
// </script>";
// 			return $html;
// 		}
?>
