<?php 
class CopyToSanitizer implements IFormSanitizer
{
	protected $directory,$rootPath;
	function __construct($directory,$rootPath=false){
		$this->directory=$directory;
		if (!$rootPath) {
			$this->rootPath = path_root;
		} else {
			$this->rootPath = $rootPath;
		}
	}
	function clean($value){
		if (is_file($this->rootPath.$value)) {
			copy($this->rootPath.$value, $this->directory."/".basename($value));
			return str_replace(path_root,"",$this->directory."/".basename($value));
		} else {
			return $value;
		}
		
	}
}
?>