<?php 
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/

/**
 * Classe que serve para inserir css's pertencentes ao escopo localizados na pasta magic/scopes/[scope]/views/default/css
 */
final class ScopeCSS extends CssAbstract {
	public function __construct($file,$scope=false){
		if (!$scope) {
			$scope = scope;
		}
		$this->rootPath = "/scopes/$scope/views/default/css/";
		parent::__construct($file);
	}
}
?>