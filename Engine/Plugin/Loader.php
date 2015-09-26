<?php 
	namespace Magic\Engine\Plugin;
	use Magic\Engine\Registry;

	/**
	* 
	*/
	class Loader
	{
		static protected $loadedPlugins = array();
		static public function register($plugin){
			global $registry;
			$folder = path_root."/Plugins/".$plugin;
			$file = $folder."/".$plugin.".php";
			if (!in_array($plugin, self::$loadedPlugins)) {
				if (is_file($file)) {
					$class = "Magic\Plugins\\$plugin\\$plugin";
					$pluginObj = new $class($plugin,$folder,$registry);
					$registry->set($plugin,$pluginObj);
					self::$loadedPlugins[] = $plugin;
				} else {
					throw new Exception("Init file ($file) not found at plugin directory. ", 1);
				}
			}
		}
	}
?>