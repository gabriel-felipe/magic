<?php 
namespace Magic\Engine\Datamgr\Hooks;
class DbModelCallbackHook extends DbModelHook
{
	protected $name = "DbModelCallbackHook";
	protected $callback;
	public function __construct($callback){
		if (!is_callable($callback)) {
			throw new Exception("callbackHook needs a callback.", 1);
			
		}
		$this->callback = $callback;
	}
	public function action(Array &$params){
		$function = $this->callback;
		call_user_func_array($function,array($params));
	}
}
?>