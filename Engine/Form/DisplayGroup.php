<?php 
namespace Magic\Engine\Form;
use Magic\Engine\Form\Decorators\AbstractFormDecorator;
use Magic\Engine\Mvc\View\Compiladores\ViewCompilador;
use Magic\Engine\Mvc\View\Compiladores\InterfaceViewCompilador;

/**
* Classe para lidar com forms no backend. Validação / Limpeza de dados.
*/
class DisplayGroup extends Form
{
	
	public function getNewView(){
		return new FormView("displayGroup",$this);
	}
}
?>