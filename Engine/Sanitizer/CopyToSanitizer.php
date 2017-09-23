<?php 
namespace Magic\Engine\Sanitizer;
class CopyToSanitizer extends AbstractSanitizer
{
	protected $directory,$pathRoot;
	public $nameCallback;
	function __construct($directory,$pathRoot=false){	
		$this->pathRoot = ($pathRoot) ? $pathRoot : path_root;
		$this->directory=$directory;
		$this->nameCallback = function($name){
			return $name;
		};
	}
	function setNameCallback($callback){
		$this->nameCallback = $callback;
		return $this;
	}
	function sanitize($value){
		if (is_file($this->pathRoot.$value)) {
			$name = $this->nameCallback(basename($value));
			if (!is_dir($this->pathRoot.$this->directory)) {
				mkdir($this->pathRoot.$this->directory,0775,true);
			}
			copy($this->pathRoot.$value, $this->pathRoot.$this->directory."/".$name);
			return $this->directory."/".$name;
		} else {
			return $value;
		}
		
	}
	function __call($method,$args){
		if ($method == "nameCallback") {
			return call_user_func_array($this->nameCallback, $args);
		}
	}
}
?>