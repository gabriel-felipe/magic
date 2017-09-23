<?php 
namespace Magic\Engine\Sanitizer;
class ReplaceSanitizer extends AbstractSanitizer {
	protected $replaces;
	public function __construct($replaces=array()){
		$this->replaces = $replaces;
	}
	public function sanitize($str){
		return str_replace(array_keys($this->replaces),array_values($this->replaces),$str);
	}

}
?>