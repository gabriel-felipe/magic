<?php
namespace Magic\Engine;
final class Registry {
	private $data = array();

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : FALSE);
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}
 	   public function __get($key) {
			return $this->get($key);
	}

}
?>