<?php 
namespace Magic\Engine\Helper\Paginator\Adapter;
use \Magic\Engine\Helper\Paginator\AbstractPaginator;
/**
* 
*/
abstract class AbstractPaginatorAdapter
{
	
	protected $paginator,$adapted;

	function setPaginator(AbstractPaginator $paginator){
		$this->paginator = $paginator;
		return $this;
	}
	function __construct(AbstractPaginator $paginator,$adapted)
	{
		$this->init($adapted);
		$this->setPaginator($paginator);
		
		$this->map();
	}

	function getPaginator(){
		return $this->paginator;
	}

	function __call($method,$params){
		return call_user_func_array(array($this->paginator,$method),$params);
	}
	function __toString(){
		return $this->render();
	}
	function init($adapted){

	}
	abstract function map();

}

?>