<?php 
/**
 * @package MagicDocument\Script
 **/

/**
* Classe responsável por gerenciar os scripts do documento.
*/
class ScriptManager
{
	protected $scripts = array();
	/**
	 * Adiciona um script ao gerenciador
	 * @param ScriptAbstract $script Script que vai ser adicionado.
	 */
	public function addScript(ScriptAbstract $script){
		$this->scripts[md5($script->getAbsPath())] = $script;
	}

	public function getScripts($pos=false){
		$result = array();
		if ($pos) {
			foreach ($this->scripts as $k=>$script) {
				if ($script->getPosition() == $pos) {
					$result[$k] = $script->toString();
				}
			}
		} else {
			foreach ($this->scripts as $k=>$script) {
				$result[$k] = $script->toString();
			}
		}
		return $result;
	}

}
?>