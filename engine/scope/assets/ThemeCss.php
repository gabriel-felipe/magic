<?php 
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/

/**
 * Classe que serve para inserir css's pertencentes ao escopo localizados na pasta magic/scopes/[scope]/views/default/css
 */
final class ThemeCSS extends CssAbstract {
	public function __construct($file,$scope){
		$c = 0;
		foreach ($scope->getThemes() as $theme) {
			$c++;
			$this->rootPath = "/scopes/".$scope->getName()."/views/".$theme."/css/";
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