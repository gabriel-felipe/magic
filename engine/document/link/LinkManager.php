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
final class LinkManager
{
	private $links=array();
	protected $cacheFolder;
	protected $data;
	protected $less;
	public function __construct(){
		global $registry;
		$this->less = $registry->get("less");
		$this->cacheFolder = path_cache."/document/style/";
	}
	/**
	 * Adiciona um link ao gerenciador
	 * @param LinkAbstract $link Link que vai ser adicionado.
	 */
	public function addLink(LinkAbstract $link){
		$this->links[md5($link->getAbsPath())] = $link;
	}

	/**
	 * Retorna todos os links.
	 * @param boolean $compileLocals Se true, compila todos os css's locais em um só, do contrário traz link a link normalmente.
	 */
	public function getLinks($compileLocals=1){
		$result = array();
		if (!$compileLocals) {
			foreach($this->links as $link){
				$result[$link->getAbsPath()] = $link->toString();
			}
			return $result;
		} else {
			foreach($this->links as $link){
				if (!$link->getIsLocal() or $link->getRel() != "stylesheet") {
					$result[$link->getAbsPath()] = $link->toString();	
				}
			}
			$allCached = $this->compileLocalStyles();
			$result[$allCached->getAbsPath()] = $allCached->toString();
			return $result;
		}
	}

	/**
	 * Concatena todos os css locais em um só usando um compilador less.
	 * @return AbstractCss retorna um objeto abstract css referindo o caminho do novo arquivo de cache gerado.
	 */
	public function concatLocalStyles(){
		$result = "";
		foreach($this->links as $link){
			if ($link->getIsLocal() and $link->getRel() == "stylesheet") {
				$result .= $link->getContent()."\n";
			}
		}
		return $result;
	}

	/**
	 * Compila todos os css locais em um só usando um compilador less.
	 * @return AbstractCss retorna um objeto abstract css referindo o caminho do novo arquivo de cache gerado.
	 */
	public function compileLocalStyles(){
		$filename = $this->getCachePath();
		if ($this->shouldRegenerateCache()) {

			$result = "";
			foreach($this->links as $link){
				if ($link->getIsLocal() and $link->getRel() == "stylesheet") {
					$result .= $link->compile()."\n";
				}
			}
			$result = $this->minify($this->less->compile($result));
			$file = fopen($filename, "w+");
			fwrite($file, $result);
			fclose($file);
		}
		return new CacheCss(basename($filename));
	}

	/**
	 * Retorna o nome do arquivo de cache.
	 */
	public function getCachePath(){
		$result = "";
		foreach($this->links as $link){
			if ($link->getIsLocal() and $link->getRel() == "stylesheet") {
				$result .= serialize($link);
			}
		}
		$filepath = $this->cacheFolder.md5($result).".css";
		return $filepath;
	}

	/**
	 * Retorna a timestamp de última modificação dos css
	 */
	public function getLastStyleMod(){
		$lastMod = 0;
		foreach($this->links as $link){
			if ($link->getIsLocal() and $link->getRel() == "stylesheet") {
				$modDate = $link->getModDate();
				if ($modDate > $lastMod ) {
					$lastMod = $modDate;
				}	
			}
		}
		return $lastMod;
	}

	/**
	 * Checa se o cache css para os css's locais inseridos deve ser regerado.
	 */
	public function shouldRegenerateCache(){
		$cacheFile = $this->getCachePath();
		if (!is_file($cacheFile)) { //Se o arquivo não existir gerar.
			return true;
		}
		$modDate = filectime($cacheFile);
		if ($modDate < $this->getLastStyleMod()) { //Se um css foi modificado depois de o arquivo de cache
			return true;
		}
		return false; //retorna falso se nenhuma das condições acima for atingida.
	}

	/**
	 * Minifica uma string de css.
	 * @param  string $content string de css a ser minificada
	 * @return string minificada de css
	 */
	public function minify($content){
		return $content;
		// Remove comments
		$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

		// Remove space after colons
		$content = str_replace(': ', ':', $content);

		// Remove whitespace
		$content = str_replace(array("\r\n", "\r", "\n", "\t"), '', $content);

		// Collapse adjacent spaces into a single space
		$content = preg_replace(" {2,}", ' ',$content);

		// Remove spaces that might still be left where we know they aren't needed
		$content = str_replace(array('} '), '}', $content);
		$content = str_replace(array('{ '), '{', $content);
		$content = str_replace(array('; '), ';', $content);
		$content = str_replace(array(', '), ',', $content);
		$content = str_replace(array(' }'), '}', $content);
		$content = str_replace(array(' {'), '{', $content);
		$content = str_replace(array(' ;'), ';', $content);
		$content = str_replace(array(' ,'), ',', $content);
		return $content;
	}

	
}
?>