<?php
namespace Magic\Engine\Mvc;
use \Exception;
use Magic\Engine\Scope\Scope;
class ActionException extends Exception
{
	
}
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
	protected $scope;
	protected $view;
	public function __construct($route,Scope $scope, $args = array(),$registry=array()) {
		$this->route = $route;
		$this->registry = $registry;
		$this->scope = $scope;
		$path = '';
		$this->view = $route;
		$parts = explode('/', str_replace('../', '', (string)$route));
		
		foreach ($parts as $part) { 
			$path .= $part;

			if (is_dir($scope->getFolder() . '/controller/' . $path)) {
				$path .= '/';
				
				array_shift($parts);
				
				continue;
			}
			
			if (is_file($scope->getControllerFolder() . '/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php')) {
				$this->file = $scope->getControllerFolder() . '/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';
				
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

	public function getRoute(){
		return $this->route;
	}

	public function getController(){
		$class = $this->class;			
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
			$obj = new $class($this->registry,$this->scope);
			try {
				$obj->setView($this->registry->get("scope")->getView($this->view));	
			} catch (Exception $e) {
				//Do nothing.
			}
			
			return $obj;
		}	
	}
	public function execute() {
		try {

			if($this->is_ok){
				
				$method = $this->method;
				$errors = array();
				

				$controller = $this->getController();
				if(!method_exists($controller, $method)){
					$errors[] = "Error with method. Method ".$method." doesn't exist.";	
				}
				if(count($errors) > 0){
					throw new Exception("Error Processing Request. <br />\n".implode("<br />\n", $errors). ", 1");
				}

				if(!$this->registry->get('page')){
					$this->registry->set("page",$controller);
				}

				// var_dump($controller);

				$return = call_user_func_array(array($controller,$method), $this->getArgs());
				return $return;
			} else {				
				throw new ActionException("Error Processing Action(route: \"{$this->route}\"), Pls Check.", 1);
			}
		} catch(ActionException $e) {
			$this->htmlError->triggerDefault(404);
			die();
		} catch(Exception $e){
			throw $e;
		}
	}
	function __get($name){
		return $this->registry->get($name);
	}
}
?>