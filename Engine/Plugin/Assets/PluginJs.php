<?php 
namespace Magic\Engine\Plugin\Assets;
use Magic\Engine\Document\Script\ScriptAbstract;
/**
 * @package MagicDocument\Plugin\Assets
 **/

/**
 * Classe que serve para inserir scripts de um determinado plugin, localizados na pasta magic/plugins/[plugin]/js
 *
 */
final class PluginJs extends ScriptAbstract {
	public function __construct($file,$plugin){
		$this->rootPath = "/Plugins/$plugin/js/";
		parent::__construct($file);
	}
}
?>