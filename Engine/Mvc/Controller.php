<?php
	namespace Magic\Engine\Mvc;
	use Magic\Engine\Hooks\HookChainManager;
	use Magic\Engine\Hooks\AbstractHook;
	use Magic\Engine\Scope\Scope;
	abstract class Controller {
		//Variável responsável por classes e informaçadicionais
		public $registry;
		protected $view;
		protected $children = array();
		protected $controllerHooks;
		public function __construct($registry=array(),Scope $scope){

			$this->scope = $scope;
			$this->registry = $registry;
			$this->view = $this->scope->getView();
			$this->controllerHooks = new HookChainManager;
			$this->after_construct();
		}
		public function registerHook(AbstractHook $hook, $chainName){
			$this->controllerHooks->registerHook($hook,$chainName);
		}
		
		protected function after_construct(){

		}

		public function setViewPath($path,$checkExistence=1){
			$this->view->setPath($path,$checkExistence);	
		}

		public function getChild($child, $args = array()) {
			$this->controllerHooks->callChain("beforeGetChild",array($this,$child,$args));
			$action = new Action($child,$this->scope, $args,$this->registry);

			ob_start();
				$action->execute();
			$exec = ob_get_clean();
			$this->controllerHooks->callChain("afterGetChild",array($this,$child,$args));
			return $exec;

		}

		public function getView($view, $args = array()) {

			$view = $this->scope->getView($view);
			$view->mergeData($args);
			return $view->render();
			
		}

		public function setView($view){
			$this->view = $view;
        }

		public function addChildren($children){
			$this->children[] = $children;
		}
		public function getChildren(){
			$data = array();

			foreach ($this->children as $child) {
				$name = explode("/",$child);
				$name = end($name);
				$name = $name;
				$content = $this->getChild($child);				
				$data[$name] = $content;
			}
			return $data;
		}

		protected function getContent() {
			$this->controllerHooks->callChain("beforeGetContent",array($this));
			$this->hooks->callChain("beforeGetContent",array(&$this));
			$data = $this->getChildren();
			$this->view->mergeData($data);
			$this->hooks->callChain("afterGetContent",array(&$this));
			$this->controllerHooks->callChain("afterGetContent",array($this));

			return $this->view->render();
		}
		
		
	    public function __get($key) {
			return $this->registry->get($key);
		}
	}	
?>