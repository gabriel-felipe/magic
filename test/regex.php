<?php 
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('display_errors',1); 
ini_set('html_errors', 'On');
define('ns','teste');

class url {
	private $params = array(
		'ns' => array('admin','public'),
		'route' => array('regex'=>'/^[A-z0-9\/_-]+$/')
	);
	private $urls = array(
		array(
		'format' => 'route',
		'default_params' => array('ns'=>ns)
		),
		array(
		'format' => 'ns/route/id?',
		'params' => array('id'=>array('regex'=>'/^[0-9]+$/'))
		),
		array(
		'format' => 'id/route',
		'params' => array('id'=>array('regex'=>'/^[0-9]+$/')),
		'default_params' => array('ns'=>'public')
		)
	);
	private $final_url = array();
	function analyze($url){
		$final = 
		$parts = $this->decompose($url); //Get array with pieces
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
			
			
			print_R($_GET);
			
		}
	}

	public function compose($parts){
		$tmp_parts = $parts;
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
		echo $final_url;

	}

	private function match($value,$options,$required){
		

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
	}

	private function sanitize_final_url(){
		$this->final_url['route'] = str_replace('-','/',$this->final_url['route']);
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
	
}
$url = new url;
$url->analyze($_GET['route']);
echo "<br />";
$url->compose(array('route'=>'teste-route','id'=>30));
?>