<?php 
	namespace Magic\Engine\Authentication;
	use Magic\Engine\Hooks\HookChainManager;
	use Magic\Engine\Hooks\AbstractHook;
	class Identity {
		protected $credential;
		protected $hookManager;
		protected $data=array();
		public function __construct(){
			$this->hookManager = new HookChainManager;
			$this->hookManager->setGlobalParams(array($this));
		}
		public function setCredential($credential){
			$this->credential = $credential;
			return $credential;
		}
		public function getCredential(){
			return $this->credential;
		}
		public function logMeIn($data){
			$this->setData($data);
			$this->hookManager->callChain("loggedIn");
		}
		public function logMeOut(){
			$this->hookManager->callChain("loggedOut");
			return true;
		}
		public function setData($data){
			$this->data = $data;
			return $this;
		}
		public function getData(){
			return $this->data;
		}
		public function registerHook(AbstractHook $hook, $chainName){
			$this->hookManager->registerHook($hook,$chainName);
		}


		public function callHookChain($chain){
			$this->hookManager->callChain($chain);
		}

		public function __get($key){
			return (isset($this->data[$key])) ? $this->data[$key] : false;
		}
	}
?>