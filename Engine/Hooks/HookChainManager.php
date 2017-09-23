<?php 
namespace Magic\Engine\Hooks;
/**
* Gerenciador de várias HookChains
*/
class HookChainManager
{
    protected $HookChains=array();
    protected $globalParams=array();
    public function registerChain($name,HookChain $chain){
        $this->HookChains[$name] = $chain;
    }
    public function setGlobalParams(array $params=array()){
        $this->globalParams = $params;
    }
    public function getGlobalParams(){
        return $this->globalParams;
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
    public function getHookChains(){
        return $this->HookChains;
    }
    public function callChain($name,$params=array()){
        $params = array_merge($this->globalParams,$params);
        $chain = $this->getChain($name);
        if ($chain) {
            return $chain->call($params);
        }
        return false;
    }
    function __clone(){
        $array = array();
        foreach ($this->HookChains as $key => $value) {
            $array[$key] = clone $value;
        }
        $this->HookChains = $array;
    }

}
?>