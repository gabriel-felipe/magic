<?php 
/**
 * @package MagicDocument\Plugin\Assets
 **/

/**
 * Classe que serve para inserir scripts de um determinado plugin, localizados na pasta magic/plugins/[plugin]/js
 *
 */
final class PluginJs extends ScriptAbstract {
	public function __construct($file,$plugin){
		$this->rootPath = "/plugins/$plugin/js/";
		parent::__construct($file);
	}
}
?>