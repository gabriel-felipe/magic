<?php 
/**
 * @package MagicDocument\Meta
 **/

/**
* Classe responsável por gerenciar todas as meta tags.
*/
class MetaManager
{
	protected $metas = array();
	/**
	 * Adiciona uma Meta ao gerenciador
	 * @param Meta $meta Meta que vai ser adicionada.
	 */
	public function addMeta(Meta $meta){
		$this->metas[$meta->getAlias()] = $meta;
	}


	/**
	 * Remove uma Meta do gerenciador
	 * @param Meta $meta Meta que vai ser removida.
	 */
	public function dropMeta($alias){
		if (isset($this->metas[$alias])) {
			unset($this->metas[$alias]);
		}
	}

	/**
	 * Retorna todas as metas em formato de string
	 * @return string
	 */
	public function getMetas(){
		$result = array();
		foreach ($this->metas as $k=>$Meta) {
			$result[$k] = $Meta->toString();
		}
		return $result;
	}

}
?>