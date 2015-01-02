<?php 
/**
* Gerenciador de várias HookChains
*/
class HookChainManager
{
	protected $HookChains=array();
	public function registerChain($name,HookChain $chain){
    	$this->HookChains[$name] = $chain;
    }
    public function getChain($name){
    	return (isset($this->HookChains[$name])) ? $this->HookChains[$name] : false;
    }
    public function registerHook(AbstractHook $hook, $chainName){
    	$chain = $this->getChain($chainName);
    	if (!$chain) {
    		$chain = ChainFactory::getChain();
    	}
    	$chain->registerHook($hook);
    	$this->registerChain($chainName,$chain);
    	return $this;
    }
    public function callChain($name,$params=array()){
    	$chain = $this->getChain($name);
    	if ($chain) {
    		$chain->call($params);
    	}
    	return $this;
    }

}
?>