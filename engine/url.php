<?php
	final class url {
		public $ns = "";
		private $params = array(
			'ns' => array('admin','public'),
			'route' => array('regex'=>'/^[A-z0-9\/_-]+$/')
		);
		private $urls = array();
		private $shortcuts = array();
		public function __construct(){
			$this->urls = array(
				array(
				'format' => 'ns',
				'default_params' => array('route'=>'x'),
				),
				array(
				'format' => 'route',
				'default_params' => array('ns'=>$this->ns),
				),
				array(
				'format' => 'ns/route',
				),
				array(
				'format' => 'id/route',
				'params' => array('id'=>array('regex'=>'/^[0-9]+$/')),
				'default_params' => array('ns'=>$this->ns)
				),
				array(
				'format' => 'ns/route/id?',
				'params' => array('id'=>array('regex'=>'/^[0-9]+$/')),
				),
			);
		}
		public function add_shortcut($route,$params){
			$this->shortcuts[$route] = $params;
		}
		public function set_ns($ns){
			$this->ns = $ns;
			$this->__construct();

		}
		private $final_url = array();
		function analyze($url){
			$url = trim($url,"/");
			$parts = $this->decompose($url); //Get array with pieces
			foreach($this->shortcuts as $k=>$sc){
				$tmp = $sc;
				$url_c = $this->decompose($k);
				$match = true;
				foreach($parts as $kp => $part){
					if(array_key_exists($kp, $url_c)){
						if(preg_match("/\[([^\]]+)\]/",trim($url_c[$kp]),$matches)){
							

							$pos = stripos($url_c[$kp],"[".$matches[1]."]");
							$posf = strlen("[".$matches[1]."]") + $pos;
							$str_anterior = substr($url_c[$kp],0,$pos);
							$str_f = substr($url_c[$kp],$posf,strlen($url_c[$kp]));						
							
							$part = str_replace($str_anterior,"",$part);
							$part = str_replace($str_f,"",$part);
							$url_c[$kp] = $part;
							$tmp[$matches[1]] = $part;
						}
						if($part == $url_c[$kp]){
							continue;
						} else {
							$match = false;
							break;
						}
					} else {
						$match = false;
						break;
					}
				}
				if($match){
					
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

		public function compose($parts){
			$tmp_parts = $parts;
			$tmp_parts_shortcut = $parts;
			foreach($tmp_parts_shortcut as $k=>$pass){
				
				if(!array_key_exists($k, $this->params)){
					$tmp_parts_shortcut[$k] = '?';
				}
			}
			
			if(in_array($tmp_parts_shortcut,$this->shortcuts)){
				$link = array_search($tmp_parts_shortcut, $this->shortcuts);
				foreach($parts as $k=>$pass){
					if(!array_key_exists($k, $this->params)){
						$link = str_replace("[$k]",$pass,$link);
					}
				}
				return $link;
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
				} else {
					
				}
				if($match){
					break;
				}
			}
			if($final_url){
				return $final_url;
			} else {
				trigger_error("Erro ao gerar url ".print_r($tmp_parts,true), E_USER_ERROR);
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
			$this->final_url['route'] = str_replace('_','/',$this->final_url['route']);
			foreach($this->final_url['params'] as $p=>$v){
					$_GET[$p] = $v;
			}
			$_GET['ns'] = $this->final_url['ns'];
			$_GET['route'] = $this->final_url['route'];
			return true;
		}
		private function reset_final_url($params=array()){
			$this->final_url = array("ns"=>'','route'=>'', 'params'=>array());
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
		function get($route,$params=false,$ns=false){
			$paramsFinal = array('route'=>str_replace("/","_",data::cleaner($route,"url")));
			if($ns){
				$params['ns'] = data::cleaner($ns);
			} else {
				$params['ns'] = ns;
			}
			if($params['ns'] == 'public'){
				unset($params['ns']);
			}
			
			if(is_array($params)){
				$paramsFinal = array_merge($paramsFinal,$params);
			}
			$url = $this->compose($paramsFinal);
			$url = ($url) ? $url."/" : "";
			return base_url."/".$url;
		}
		function redirect($link,$params=false,$ns=false){
			require_once(path_library."/data-cleaner.php");
			if(!validate::absolute_url($link)){
				$link = $this->get($link,$params,$ns);
			}
			echo "<meta http-equiv=\"refresh\" content=\"0; url=$link\">";
		}
	}
?>