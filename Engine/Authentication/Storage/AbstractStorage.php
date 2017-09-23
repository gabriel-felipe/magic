<?php 
	namespace Magic\Engine\Authentication\Storage;
	abstract class AbstractStorage {
		protected $context;
		abstract public function write($identity);
		abstract public function read();
		abstract public function clear();
		abstract public function isEmpty();
		function getContext(){
			return $this->context;
		}
		function setContext($context){
			$this->context = $context;
			return $this;
		}
	}
?>