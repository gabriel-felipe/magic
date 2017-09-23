<?php 
namespace Magic\Engine\Hooks;
class HookChain
{
	protected $hooks;
    public function registerHook(AbstractHook $hook){
    	$this->hooks[$hook->getName()] = $hook;
    }
    public function getHooks(){
    	return $this->hooks;
    }
   	public function call($params=array()){
		foreach ($this->hooks as $obj) {
			$params = $obj->run($params);
			if (!is_array($params)) {
				throw new Exception($obj->getName()." should return an array so next hook can continue chain.", 1);
				
			}
		}
		return $params;
	}
	function __clone(){
		$array = array();
        foreach ($this->hooks as $key => $value) {
            $array[$key] = clone $value;
        }
        $this->hooks = $array;
	}
}
?>