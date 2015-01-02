<?php 
namespace Magic\Engine\Plugin\Assets;
use Magic\Engine\Document\Link\Css\CssAbstract;
/**
 * This is a file
 * @package MagicDocument\Plugin\Assets
 **/

/**
 * Classe que serve para inserir css's pertencentes a um plugin. Os css's estão localizados na pasta magic/plugins/[scope]/css
 */
final class PluginCSS extends CssAbstract {
	public function __construct($file,$plugin=false){
		if (!$plugin) {
			$plugin = scope;
		}
		$this->rootPath = "/Plugins/$plugin/css/";
		parent::__construct($file);
	}
}
?>