<?php 
/**
* Classe responsável por ler arquivos json e parsea-los em configurações acessíveis através de um objeto.
*/
class config
{
	protected $data=array();
	function __construct($file=false)
	{
		if ($file) {
			$this->load($file);
		}
	}
	function load($file){
		if (file_exists($file)) {
			return $this->parse(file_get_contents($file));
		} else {
			throw new Exception("Configuration file ($file) was not found.", 1);
		}
	}
	function loadIfExists($file){
		if (file_exists($file)) {
			return $this->parse(file_get_contents($file));
		} else {
			return $this;
		}	
	}
	function parse($data){
		$data = json_decode($data,true);
		if (is_array($data)) {
			$this->data = array_merge($this->data,$data);
			return $this;
		} else {
			throw new Exception("Could not decode $data as a json. Please check file format", 1);
		}
	}
	function set($key,$data){
		$this->data[$key] = $data;
	}
	function get($key){
		return (isset($this->data[$key])) ? $this->data[$key] : false;
	}
	function getData(){
		return $this->data;
	}
	function __get($name){
		return $this->get($name);
	}
}
?>