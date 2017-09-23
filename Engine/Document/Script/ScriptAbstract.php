<?php
namespace Magic\Engine\Document\Script;
use Magic\Engine\Document\AbstractAsset;
/**
 * This is a file
 * @package MagicDocument\Script
 **/
/**
* Classe base para todas as classes de scripts.
* @property string $position define qual a posição do script. Os valores de $this->positions são as possibilidades.
* @property array positions define todas as posições possíveis.
*/
Abstract class ScriptAbstract extends AbstractAsset
{
	protected $position="bottom";
	protected $positions = array("top","bottom");
	protected $registry;

	function __construct($file,$position="bottom"){
		parent::__construct($file);
		$this->setPosition($position);

	}

	/**
	 * Define $this->position confore o parâmetro position.
	 */
	public function setPosition($position){
		if (!in_array($position, $this->positions)) {
			throw new Exception("Posição para script não encontrada", 1);
			return false;
		} else {
			$this->position = $position;
			return true;
		}

	}

	public function getPosition(){
		return $this->position;
	}
	function toString(){
		$modDate = ($this->getModDate()) ? $this->getModDate() : 1;
        return "<script src='".$this->getRelPath()."?v=".$modDate."'></script>";
    }
    function __toString(){
        return $this->toString();
    }
}
?>
