<?php 
/**
* Plugin responsável por inserir os assets padrões do Magic ao documento.
*/
class MagicDefaultViewCompiladorPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendCompiladoresView();
	}
	function appendCompiladoresView(){
		require_once("MagicDefaultViewCompiladorDecorator.php");
		$this->ViewHandler->addCompiladorDecorator(new MagicDefaultViewCompiladorDecorator());
	}
}
?>
