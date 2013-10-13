<?php
final class Action {
	protected $file = false;
	protected $class = false;
	protected $method = false;
	protected $args = array();
	protected $is_ok = false;
	protected $registry;
	protected $route = false;
	protected $errors = array();
	public $css = array();
	public $js = array();
	public $js_inline = array();
	public $css_inline = array();
	public function __construct($route, $args = array(),$registry=array()) {
		$this->route = $route;
		$this->registry = $registry;
		$path = '';
		
		$parts = explode('/', str_replace('../', '', (string)$route));
		echo path_scope."\n";
		foreach ($parts as $part) { 
			$path .= $part;

			if (is_dir(path_scope . '/controller/' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			if (is_file(path_controllers . '/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php')) {
				$this->file = path_controllers . '/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';
				
				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

				array_shift($parts);
				
				break;
			}
		}
		
		if ($args) {
			$this->args = $args;
		}			
		$method = array_shift($parts);
			
		if ($method) {
			$this->method = $method;
		} else {
			$this->method = 'index';
		}
		if($this->method and $this->file and $this->class){
			$this->is_ok = true;
		}
	}
	
	public function getFile() {
		return $this->file;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getArgs() {
		return $this->args;
	}
	public function get_controller(){
		if($this->is_ok){		
			$class = $this->class;
			$method = $this->method;
			
			$errors = array();
			if(!$this->file or !file_exists($this->file)){
				$errors[] = "Error with file: ".$this->file."; Please, check if the file exists.";
			} else {
				require_once($this->file);
			}
			
			if(!class_exists($class)){
				$errors[] = "Error with class. Class ".$class." doesn't exist.";
			}
			if(count($errors) > 0){
				return false;
			} else {
				$obj = new $class;
				return $obj;
			}	
		}
	}
	public function execute() {
		try {
			if($this->is_ok){
				
				$class = $this->class;
				$method = $this->method;
				
				$errors = array();
				if(!$this->file or !file_exists($this->file)){
					$errors[] = "Error with file: ".$this->file."; Please, check if the file exists.";
				} else {
					require_once($this->file);
				}
				
				if(!class_exists($class)){
					$errors[] = "Error with class. Class ".$class." doesn't exist.";
				}
				$obj = new $class;
				if(!method_exists($obj, $method)){
					$errors[] = "Error with method. Method ".$method." doesn't exist.";	
				}
				if(count($errors) > 0){
					throw new Exception("Error Processing Request. <br />\n".implode("<br />\n", $errors). ", 1");
				}
				
				$controller = new $class($this->registry);
				if(!$this->registry->get('page')){
					$this->registry->set("page",$controller);
				}
				$return = $controller->$method();
				$this->css = $controller->css_linked;
				$this->js = $controller->js_linked;
				$this->css_inline = $controller->css_inline;
				$this->js_inline = $controller->js_inline;
				return $return;
			} else {
				echo "FILE {$this->file} <br /> CLASS {$this->class} <br /> METHOD {$this->method} <br />";
				
				throw new Exception("Error Processing Action(route: \"{$this->route}\"), Pls Check.", 1);
				
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
}
?>