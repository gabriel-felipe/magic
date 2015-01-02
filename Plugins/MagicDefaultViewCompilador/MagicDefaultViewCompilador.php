<?php 
namespace Magic\Plugins\MagicDefaultViewCompilador;
use Magic\Engine\Plugin\AbstractPlugin;
/**
* Plugin responsável por inserir os assets padrões do Magic ao documento.
*/
class MagicDefaultViewCompilador extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendCompiladoresView();
	}
	function appendCompiladoresView(){
		$this->ViewHandler->addCompiladorDecorator(new MagicDefaultViewCompiladorDecorator());
	}
}
?>
