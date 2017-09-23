<?php
	namespace Magic\Engine\Mvc;
	use \data;
	final class Url {
		static protected $instance=false;
		public $scope = "";
		private $params = array(
			'scope' => array(''),
			'route' => array('regex'=>'/^[A-z0-9\/%_-]+$/')
		);
		private $shortcuts = array();
		protected function __construct($scope=false){
			if ($scope) {
				$this->setScope($scope);
			}
		}

		static function getInstance(){
			if (!self::$instance) {
				$c = __CLASS__;
				self::$instance = new $c();
			}
			return self::$instance;
		}
		public function addShortcut($route,$params){
			$this->shortcuts[$route] = $params;
		}
		public function setScope($scope){
			$this->scope = $scope;
			$this->params['scope'] = $scope;
		}
		public function getScope(){
			return $this->scope;
		}
		private $final_url = array();
		function analyze($url){

			$url = trim($url,"/");
			$parts = $this->decompose($url); //Get array with pieces
			foreach($this->shortcuts as $k=>$sc){ //Analizar cada atalho
				$tmp = $sc; //Armazenando informações do atalho numa variável temporária
				$url_c = $this->decompose($k); //Quebrando a url amigável do atalho em parts;
				$match = true;
				if(count($url_c) != count($parts)){ //Se não tiver o mesmo número de pedaços já pula para a próxima.
					$match = false;
					continue;
				}
				$cp = 1;
				foreach($parts as $kp => $part){

					if(array_key_exists($kp, $url_c)){

						if(preg_match("/\{(.+)\}/",trim($url_c[$kp]),$matches)){ //Verificando se essa parte é um parametro						

							if(preg_match("/".$matches[1]."/",$part)){ //Verificando se a regex bate com o que foi passado
								foreach($tmp as &$tmp_part){ // Se bater faz as substituições nos parametros
									$tmp_part = str_replace("$".$cp,$part,$tmp_part); 
								}
							} else { //se não pula para o próximo atalho.
								$match = false;
								continue;
							}
							$cp++;
						} else { // Se não for um parametro, verifica se as duas parte são identicas.
							if($part != $url_c[$kp]){ // Se não forem, pula para o próximo atalho.
								$match = false;
								continue;
							}
						}
					} else {
						$match = false;
						break;
					}
				}
				
				if($match){ // Se bater as regras prepara os dados no $_GET.

					if(array_key_exists("defaults", $sc) and is_array($sc['defaults'])){
						foreach($sc['defaults'] as $key=>$val)
							$_GET[$key] = $val;
						
					}
					unset($tmp['defaults']);
					$this->resetFinalUrl($tmp);
					$this->sanitizeFinalUrl();
					return true;
				}
			}
			
			if($match){

				$this->sanitizeFinalUrl();
				
			}
		}

		public function compose($parts,$reentered=false){
			$tmp_parts = $parts;
			foreach($this->shortcuts as $url=>&$shortcut){
				
				foreach($parts as $key => $value){
					$defaults = (isset($shortcut['defaults'])) ? $shortcut['defaults'] : array();
					if(!array_key_exists($key, $shortcut) and !array_key_exists($key, $defaults)){ //se for passado parametros a mais que o atalho aceita pula pro próximo

						continue 2;
					}
				}
				$tmp_parts_shortcut = explode("/",$url);//Pegando os pedaços da url do atalho

				$paramsOnShortcut = array(); //array para armazenar as chaves => regex dos pedaços do atalho
				$count = 1;
				$mapParamsCount = array();
				foreach($tmp_parts_shortcut as $k=>$piece){
					if(preg_match("/\{(.+)\}/",trim($piece),$matches)){ //Verificando se essa parte é um parametro
						$paramsOnShortcut[$count] = $matches[1]; //Armazena os parametros
						$mapParamsCount[$count] = $k;
						$count++;
					}
				}
				foreach($paramsOnShortcut as $k=>$regex){ //Para os pedaços variáveis na url
					foreach($shortcut as $paramName => $paramShortcut){ //Para cada pedaço do atalho
						if(!is_array($paramShortcut) and strpos($paramShortcut,"\$".$k) !== false){ //Se o pedaço não for um array e existir a combinação ${chave do pedaço que varia}, ou seja se esse pedaço do atalho usar o pedaço variável da url em questão
							if(!array_key_exists($paramName, $tmp_parts)){ //Se esse parametro é variável e ele não foi fornecido nos parametros iniciais
								continue 3; //Vai para o próximo atalho, esse não bate.
							}
							$parametroFornecido = $parts[$paramName];

							if(preg_match("/".$regex."/",$parametroFornecido)){ //Verificando se o parametro bate com a regex
								$key = $mapParamsCount[$k];
								$tmp_parts_shortcut[$key] = $parametroFornecido;
							} else { 

								continue 3; //Vai para o próximo atalho, esse parametro não bate;
							}
						}
					}
				}
				if(!isset($parts['scope'])){
					$parts['scope'] = $this->scope;
				}
				if($shortcut['route'] == $parts['route'] and $shortcut['scope'] == $parts['scope']){
					if (isset($shortcut['defaults']) and count($shortcut['defaults']) > 0) {
						foreach($shortcut['defaults'] as $key => $value){
							if(!isset($parts[$key]) or $parts[$key] != $value){
								continue 2;
							}
						}
					}
					return implode("/",$tmp_parts_shortcut);
				}
			}
			
			
			if(!$reentered) {
				
				if(isset($parts['route']) and isset($parts['scope'])){
					$url = $this->compose(array('route'=>$parts['route'],'scope'=>$parts['scope']),1);
					if ($url !== false) {
						unset($parts['route'],$parts['scope']);
					}
					
					foreach($parts as &$part)
						$part = urlencode($part);
					$query_string = http_build_query($parts);
					$url .= "?".$query_string;		
					return $url;
				} else {
					trigger_error("Erro ao gerar url ".print_r($tmp_parts,true), E_USER_ERROR);
				}
			} else {

				return false;
			}

		}

		private function match($value,$options,$required){
			
			if(is_array($options)){
				if(array_key_exists('regex', $options)){
					$regex = $options['regex'];
					if(preg_match($regex,$value)){
						return true;
					} else {
						unset($options['regex']);
						if(count($options) == 0 and $required){
							return false;
						}
					}
				}
				foreach($options as $option){
					if($option == $value){
						return true;
					}
				}
				if($required){
					return false;
				} else {
					return true;
				}		
			} else{
				return $options == $value;
			}	
		}

		private function sanitizeFinalUrl(){

			$find_query_str = preg_match("/^[^?]+\?(.+)$/" , $_SERVER['REQUEST_URI'],$query_str);
			
			if($find_query_str){
				$tmp = array();
				parse_str(rtrim($query_str[1],'/'),$tmp);
				foreach($tmp as &$t)
					$t = urldecode($t);

				$_GET = array_merge($_GET,$tmp);

			}

			$this->final_url['route'] = str_replace('_','/',$this->final_url['route']);
			foreach($this->final_url['params'] as $p=>$v){

				$_GET[$p] = $v;
			}
			$_GET['scope'] = $this->final_url['scope'];
			$_GET['route'] = $this->final_url['route'];
			return true;
		}
		private function resetFinalUrl($params=array()){
			$this->final_url = array("scope"=>'','route'=>'', 'params'=>array());
			if(is_array($params)){
				foreach($params as $param=>$value){
					$this->parseParamFinalUrl($param,$value);
				}
			}
		}
		private function parseParamFinalUrl($param,$value){
			if(array_key_exists($param, $this->final_url)){
				$this->final_url[$param] = $value;
			} else {
				$this->final_url['params'][$param] = $value;
			}
			return true;
		}

		private function decompose($url){
			$url = preg_replace("/[\/]{2,}/",'/',$url);
			$url = trim($url,'/');
			$parts = explode("/",$url);
			return $parts;
		}
		
		function get($route,$params=false,$scope=false){

			if (AUTO_GENERATE_LANGUAGE_URLS) {
				$magic_language = data::get("magic_language");
				if($magic_language){
					$params['magic_language'] = $magic_language;
				}
			}
			$paramsFinal = array('route'=>str_replace("/","_",data::cleaner($route,"url")));
			if($scope){
				$params['scope'] = data::cleaner($scope);
			} else {
				$params['scope'] = $this->scope;
			}
			if(is_array($params)){
				$paramsFinal = array_merge($paramsFinal,$params);
			}
			
			$url = $this->compose($paramsFinal);
			
			if($url !== false and $url !== "?"){
				$url = rtrim($url,"/");
				
				$url = ($url and strpos($url,"?") === false) ? $url."/" : $url;
				return base_url."/".$url;

			} else {
				$url = base_url."/index.php?route=".$route."&scope=".$paramsFinal['scope'];
				unset($paramsFinal['route']);
				unset($paramsFinal['scope']);
				if(count($paramsFinal) > 0){
					foreach($paramsFinal as $param => $value){
						$url .= "&$param=>$value";
					}
				}
				return $url;

			}
		}
		function redirect($link,$params=false,$scope=false){
			if(!\validate::absolute_url($link)){
				$link = $this->get($link,$params,$scope);
			}
			echo "<meta http-equiv=\"refresh\" content=\"0; url=$link\">";
		}
		public function getShortcuts(){
			return $this->shortcuts;
		}
	}
?>