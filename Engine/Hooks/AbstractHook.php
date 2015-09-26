<?php 
namespace Magic\Engine\Hooks;

/**
* Classe abstrata para criar e chamar gatilhos no código.
*/
abstract class AbstractHook
{
	abstract function action(Array &$params);
	public function run(Array $params){
		$this->action($params);
		return $params;
	}
	public function getName(){
		$class = get_class($this);
		return $class;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function __get($name){
		global $registry;
		return $registry->get($name);
	}
}
?>