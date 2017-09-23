<?php 
namespace Magic\Engine\Scope\Assets;
use Magic\Engine\Document\Script\ScriptAbstract;
/**
 * This is a file
 * @package MagicDocument\Script
 **/

/**
 * Classe que serve para inserir scripts comuns ao tema, localizados na pasta magic/scopes/[scopo]/views/[tema]/js
 * z
 */
final class ThemeJs extends ScriptAbstract {
	public function __construct($file,$scope){
		$c = 0;
		foreach ($scope->getThemes() as $theme) {
			$c++;
			$this->rootPath = "/Scopes/".$scope->getName()."/views/".$theme."/js/";
			parent::__construct($file);
			if ($this->doExist()) {
				break;
			}
			if (!$this->doExist() and $c == count($scope->getThemes())) {
				throw new Exception("Js $file não foi encontrado em nenhum dos temas (".implode(",",$scope->getThemes()).") do escopo {$scope->getName()}", 1);
				
			}
		}
	}
}
?>