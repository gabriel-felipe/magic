<?php 
namespace Magic\Engine\Scope\Assets;
use Magic\Engine\Document\Link\Css\CssAbstract;
/**
 * This is a file
 * @package MagicDocument\Link\Css
 **/

/**
 * Classe que serve para inserir css's pertencentes ao escopo localizados na pasta magic/scopes/[scope]/views/css
 */
final class ScopeCSS extends CssAbstract {
	public function __construct($file,$scope){
		$this->rootPath = "/Scopes/".$scope->getName()."/Views/Css/";
		parent::__construct($file);
	}
}
?>