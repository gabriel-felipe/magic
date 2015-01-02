<?php
	final class url {
		public $scope = "";
		private $params = array(
			'scope' => array(''),
			'route' => array('regex'=>'/^[A-z0-9\/%_-]+$/')
		);
		private $urls = array();
		private $shortcuts = array();
		public function __construct(){
			if($this->scope){
				$this->params['scope'][] = $this->scope;
			}
			$this->urls = array(
				array(
				'format' => 'scope',
				'default_params' => array('route'=>'x'),
				),
				array(
				'format' => 'route',
				'default_params' => array('scope'=>$this->scope),
				),
				array(
				'format' => 'scope/route',
				),
				array(
				'format' => 'id/route',
				'params' => array('id'=>array('regex'=>'/^[0-9]+$/')),
				'default_params' => array('scope'=>$this->scope)
				),
				array(
				'format' => 'scope/route/id?',
				'params' => array('id'=>array('regex'=>'/^[0-9]+$/')),
				),
			);
		}
		public function add_shortcut($route,$params){
			$this->shortcuts[$route] = $params;
		}
		public function set_scope($scope){

			$this->scope = $scope;
			$this->__construct();

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
					$this->reset_final_url($tmp);
					$this->sanitize_final_url();
					return true;
				}
			}
			$nparts = count($parts);
			$match = false;
			
			foreach($this->urls as $k=>$url){
				$format = $url['format'];
				$parts_c = $this->decompose($format);
				$nparts_c = $this->min_max_pieces($format);
				$min = $nparts_c[0];
				$max = $nparts_c[1];
				if($nparts <= $max and $nparts >= $min){
					$params = (array_key_exists("params",$url)) ? $url['params'] : array();
					$default_params = (array_key_exists('default_params', $url)) ? $url['default_params'] : array();
					$this->reset_final_url($default_params);

					$match = true;
					foreach($parts_c as $n=>$piece){
						$required = $this->is_req($piece);
						$piece = trim($piece,"?");
						$source = (array_key_exists($piece, $params)) ? $params[$piece] : $this->params[$piece];

						$value = (array_key_exists($n, $parts)) ? $parts[$n] : '';
						$this->parse_param_final_url($piece,$value);
						if(!$this->match($value,$source,$required)){
							$match = false;
							continue 2;
						}
					}

					if($match){					
						break;
					}
				}
			}
			if($match){

				$this->sanitize_final_url();
				
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
					$parts['scope'] = scope;
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
		
			
			$final_url = false;
			foreach($this->urls as $k=>$url){
				$params = (array_key_exists("params",$url)) ? $url['params'] : array();
				$format = $url['format'];
				$parts_c = $this->decompose($format);
				$parts = $tmp_parts;
				$match = false;
				foreach($parts_c as $k=>$part){
					
					$partb = trim($part,"?");
					$required = $this->is_req($part);
					if(!array_key_exists($partb, $parts)){
						if($required)
						{
							continue 2;
						}
					} else {
						$value = $parts[$partb];
						$source = (array_key_exists($partb, $params)) ? $params[$partb] : $this->params[$partb];
						if(!$this->match($value,$source,$required)){
							continue 2;
						}
					}
				}
				
				foreach($parts as $k=>$part){
					if(!in_array($k, $parts_c) and !in_array($k."?", $parts_c)){
						continue 2;
						
					}
				}
				$nparts = count($parts);
				$nparts_c = $this->min_max_pieces($format);
				$min = $nparts_c[0];
				$max = $nparts_c[1];
				
				if($nparts <= $max and $nparts >= $min){
					$default_params = (array_key_exists('default_params', $url)) ? $url['default_params'] : array();

					$params = array_merge($default_params, $parts);
					
					$match = true;
					$final_url = str_replace('?','',$format);
					foreach($parts_c as $p=>$v){
						$v = trim($v,'?');
						$va = (array_key_exists($v, $params)) ? $params[$v] : '';
						$final_url = str_replace($v,$va,$final_url);
					}
					$final_url = trim($final_url,'/');
					$match = true;
				}
				if($match){
					break;
				}
			}
			if($final_url){
				return $final_url;
			} elseif(!$reentered) {

				if(isset($parts['route']) and isset($parts['scope'])){

					$url = $this->compose(array('route'=>$parts['route'],'scope'=>$parts['scope']),1);
					if ($url) {
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

		private function sanitize_final_url(){

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
		private function reset_final_url($params=array()){
			$this->final_url = array("scope"=>'','route'=>'', 'params'=>array());
			if(is_array($params)){
				foreach($params as $param=>$value){
					$this->parse_param_final_url($param,$value);
				}
			}
		}
		private function parse_param_final_url($param,$value){
			if(array_key_exists($param, $this->final_url)){
				$this->final_url[$param] = $value;
			} else {
				$this->final_url['params'][$param] = $value;
			}
			return true;
		}
		private function is_req($param){
			return (substr($param,-1) == '?') ? false : true;
		}
		private function decompose($url){
			$url = preg_replace("/[\/]{2,}/",'/',$url);
			$url = trim($url,'/');
			$parts = explode("/",$url);
			return $parts;
		}
		private function min_max_pieces($url){
			$parts = $this->decompose($url);

			$max = count($parts);
			$min = $max;
			foreach($parts as $part){
				if(!$this->is_req($part)){
					$min -= 1;
				}
			}
			return array($min,$max);
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
				$params['scope'] = scope;
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
			require_once(path_engine_library."/data-cleaner.php");
			if(!validate::absolute_url($link)){
				$link = $this->get($link,$params,$scope);
			}
			echo "<meta http-equiv=\"refresh\" content=\"0; url=$link\">";
		}
		public function getShortcuts(){
			return $this->shortcuts;
		}
	}
?>