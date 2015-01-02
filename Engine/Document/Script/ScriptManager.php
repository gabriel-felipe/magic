<?php 
namespace Magic\Engine\Document\Script;
use Magic\Engine\Document\AbstractAssetManager;
/**
 * @package MagicDocument\Script
 **/

/**
* Classe responsável por gerenciar os scripts do documento.
*/
class ScriptManager extends AbstractAssetManager
{
	protected $assetType = "script";

	/**
	 * Adiciona um script ao gerenciador
	 * @param ScriptAbstract $script Script que vai ser adicionado.
	 */
	public function addScript(ScriptAbstract $script){
		$this->addAsset($script);
	}

	public function getScripts($compileLocal=1){
		return $this->getAssets($compileLocal);
	}
	public function getCacheAsset($file){
		return new CacheJs($file);
	}
	public function getCacheExt(){
		return "js";
	}




}
?>