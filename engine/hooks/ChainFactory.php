<?php 
/**
* Static class for creating HookChains
*/
class ChainFactory
{
	static function getChain(){
		return new HookChain();
	}
}
?>