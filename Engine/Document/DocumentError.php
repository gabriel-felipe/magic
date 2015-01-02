<?php 
	namespace Magic\Engine\Document;
	use Magic\Engine\Registry;
	use Magic\Engine\Scope\Scope;
	use Magic\Engine\Mvc\Action;
	/**
	* Classe responsável por lidar com erros html
	*/
	class DocumentError
	{
		protected $registry;
		function __construct(Registry $registry)
		{
			$this->registry = $registry;
		}
		
		function trigger($code){
			try {
				$this->triggerAction($code);
			} catch (Exception $e) {
				try {
					$this->triggerDefault($code);

				} catch (Exception $e) {
						throw new Exception($e, 1);
				}
			
			}
		}
		function getAction($code){
			$errorInfo = $this->config->errors->get("error-".$code);
			if (is_array($errorInfo) and isset($errorInfo['scope']) and isset($errorInfo['route'])) {
				$scope = new Scope($errorInfo['scope'],$this->registry);
				$scope->init();
				$this->registry->set("scope",$scope);
				$this->registry->set("route",$errorInfo['route']);
				$action = new Action($errorInfo['route'], $scope,array(),$this->registry);
				$this->registry->set("action",$action);
				return $action;
			} else {
				throw new Exception("Could not get action for error $code", 1);
			}
		}
		function triggerAction($code){
			$action = $this->getAction($code);
			if ($action) {
				$action->execute();
				die();
			} else {
				throw new Exception("Could not run action for error $code", 1);
			}

		}

		function triggerDefault($code){
			$this->html->responseCode($code);
			$this->html->bodyClass = "error-$code";
			if (is_file(path_root."/common/templates/errors/$code.html")) {
				$this->html->setLayout("/common/templates/errors/$code.html");
			} else {
				$this->html->setLayout("/common/templates/errors/default.html");	
			}
			echo $this->html->render("ERROR $code");
			die();
		}

		function __get($name){
			return $this->registry->get($name);
		}
	}
?>