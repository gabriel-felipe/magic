<?php 
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/

/**
 * Classe que serve para inserir css's pertencentes ao escopo localizados na pasta magic/scopes/[scope]/views/css
 */
final class ScopeCSS extends CssAbstract {
	public function __construct($file,$scope){
		$this->rootPath = "/scopes/".$scope->getName()."/views/css/";
		parent::__construct($file);
	}
}
?>