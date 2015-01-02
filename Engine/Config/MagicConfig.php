<?php 
namespace Magic\Engine\Config;
/**
* Classe responsável por ler arquivos json e parsea-los em configurações acessíveis através de um objeto.
*/
class MagicConfig extends Config
{
	protected $folders = array();
	function addFolder($folder){
		if (is_dir($folder)) {
			$this->folders[$folder] = rtrim($folder,DIRECTORY_SEPARATOR);
		} else {
			throw new UnexpectedValueException("Pasta $folder não foi encontrada, e portanto é impossível procurar arquivos de configuração lá.", 1);
		}
	}
	function removeFolder($folder){
		if (isset($this->folders[$folder])) {
			unset($this->folders[$folder]);
		}
	}
	function getFolders(){
		return $this->folders;
	}
	function setFolders($folders){
		$this->folders = $folders;
	}

	function loadInAll($file){
		foreach($this->folders as $f){
			$this->loadIfExists($f."/".$file);
		}
		return $this;
	}
	function loadInAllAs($file,$nickname){
		$newObj = new MagicConfig;
		$newObj->setFolders($this->getFolders());
		$newObj->loadInAll($file);
		$this->set($nickname,$newObj);
		return $this;
	}
}
?>