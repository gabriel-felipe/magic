<?php 
/**
 * @package MagicDocument\Appends
 **/

/**
* Classe responsável por gerenciar todos os appends (blocos de conteúdo) do HTML
*/
class AppendManager
{
	protected $appends = array();
	/**
	 * Adiciona um bloco ao gerenciador
	 * @param append $append Append que vai ser adicionada.
	 */
	public function addAppend(AppendAbstract $append){
		$this->appends[$append->getAlias()] = $append;
	}


	/**
	 * Remove uma append do gerenciador
	 * @param append $append append que vai ser removida.
	 */
	public function dropAppend($alias){
		if (isset($this->appends[$alias])) {
			unset($this->appends[$alias]);
		}
	}

	/**
	 * Retorna todas as appends em formato de string
	 * @return string
	 */
	public function getAppends($prepend="*",$position=false){
		$result = array();
		foreach ($this->appends as $k=>$append) {
			if ($prepend === "*" or $append->getPrepend() == $prepend) {
				if ($position === false or $position == $append->getPosition()) {
					$result[$k] = $append->toString();
				}
			}
		}
		return $result;
	}

}
?>