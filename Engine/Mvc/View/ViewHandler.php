<?php 
namespace Magic\Engine\Mvc\View;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\AbstractViewCompiladorDecorator;
/**
* View Handler
*/
class ViewHandler
{
	protected $compilador;
	protected $data=array();
	protected $registry;
	public function __construct($registry){
		$this->registry = $registry;
	}
	public function setCompilador(InterfaceViewCompilador $compilador){
        $this->compilador = $compilador;
    }

    public function getCompilador(){
        if (!$this->compilador) {
            $this->setCompilador(new ViewCompilador());
        }
        return $this->compilador;
    }

    public function addCompiladorDecorator(AbstractViewCompiladorDecorator $decorator){
        $decorator->setCompilador($this->getCompilador());
        return $this->setCompilador($decorator);
    }
	public function prepare(AbstractView $view){
		$view->setCompilador(clone $this->getCompilador());
		$view->mergeData($this->getData());
		return $view;
	}
	public function getData(){
		return $this->data;
	}
	public function setData(array $data){
		$this->data = $data;
	}
 	public function __get($name){
        return (isset($this->data[$name])) ? $this->data[$name] : $this->registry->get($name);
    }
    public function __set($name,$value){
    	$this->data[$name] = $value;
    }

}
?>