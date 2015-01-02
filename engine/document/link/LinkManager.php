<?php 
/**
 * This is a file
 * @package MagicDocument\Link
 **/

/**
* Classe responsável por gerenciar todos os links do html.
* 
* @property array $links Array de objetos que herdam LinkAbstract
* 
* @property string $cache Caminho para a pasta onde devem ser gerados os 
* arquivos de cache com os css's compilados.
* 
* @property Less objeto Less para compilar os css's
*/
final class LinkManager extends AbstractAssetManager
{
	protected $assetType = "style";
	

	/**
	 * Adiciona um link ao gerenciador
	 * @param LinkAbstract $link Link que vai ser adicionado.
	 */
	public function addLink(LinkAbstract $link){
		$this->addAsset($link);
	}


	/**
	 * Compila todos os css locais em um só usando um compilador less.
	 * @return AbstractCss retorna um objeto abstract css referindo o caminho do novo arquivo de cache gerado.
	 */
	public function getLinks($compileLocal=1){
		return $this->getAssets($compileLocal);
	}
	public function getCacheExt(){
		return "css";
	}
	public function getCacheAsset($file){
		return new CacheCss($file);
	}

	
}
?>