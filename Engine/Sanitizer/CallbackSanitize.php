<?php 
namespace Magic\Engine\Sanitizer;
class CallbackSanitize extends AbstractSanitizer {
	
	protected $callback;
	public function setCallback($callback){
		if (is_callable($callback)) {
			$this->callback = $callback;
			return $this;
		}
		throw new Exception("Callback must be callable.", 1);
		

	}
	public function sanitize($var){
		return call_user_func_array($this->callback,array($var));
	}

}
?>