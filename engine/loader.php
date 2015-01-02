<?php
	final class Loader {
		protected $registry;
		protected $file;
		protected $loadedPlugins = array();
		public function __construct($registry){
			$this->registry = $registry;
		}
		public function library($library){
			$file = path_library."/".$library.".php";
			
			if (is_file($file)) {
				require_once($file);
			} else {
				return false;
			}
		}
		public function model($model,$alias=false,$params=false){
			if(!$alias){
				$alias = "M".$model;
			}
			$file = $this->scope->getModelFolder()."/".$model.".php";
			if($params) {
				if(!is_array($params)){
					$params = array($params);
				}
			} else {
				$params = array();
			}
		

			if (is_file($file)) {
				
				require_once($file);
				$class = preg_replace('/[^a-zA-Z0-9]/', '', $model);
				$reflection_class = new ReflectionClass($class);
				$obj = $reflection_class->newInstanceArgs($params);
				$this->registry->set($alias,$obj);

			}

		}
		public function plugin($plugin){
			$folder = path_root."/plugins/".$plugin;
			$file = $folder."/".$plugin."Plugin.php";
			if (!in_array($plugin, $this->loadedPlugins)) {
				if (is_file($file)) {
					require($file);
					$class = $plugin."Plugin";
					$pluginObj = new $class($plugin,$folder,$this->registry);
					$this->registry->set($plugin,$pluginObj);
					$this->loadedPlugins[] = $plugin;
				} else {
					throw new Exception("Init file ($file) not found at plugin directory. ", 1);
				}
			}
		}
		public function __get($name){
			return $this->registry->get($name);
		}
	}