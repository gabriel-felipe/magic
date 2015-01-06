<?php 
namespace Magic\Engine\Scope\Assets;
use Magic\Engine\Document\Script\ScriptAbstract;
/**
 * This is a file
 * @package MagicDocument\Script
 **/

/**
 * Classe que serve para inserir scripts comuns ao scopo, localizados na pasta magic/scopes/[scopo]/views/js
 * z
 */
final class ScopeJs extends ScriptAbstract {
	public function __construct($file,$scope){
		$this->rootPath = "/Scopes/".$scope->getName()."/views/js/";
		parent::__construct($file);
	}
}
?>