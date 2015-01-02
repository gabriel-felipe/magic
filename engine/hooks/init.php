<?php 
require_once(path_engine."/hooks/AbstractHook.php");
require_once(path_engine."/hooks/ChainFactory.php");
require_once(path_engine."/hooks/HookChain.php");
require_once(path_engine."/hooks/HookChainManager.php");
$hooks = new HookChainManager;
$registry->set("hooks",$hooks);