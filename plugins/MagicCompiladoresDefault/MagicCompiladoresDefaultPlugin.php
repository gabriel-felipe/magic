<?php 
/**
* Plugin responsável por inserir os assets padrões do Magic ao documento.
*/
class MagicCompiladoresDefaultPlugin extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendCompiladoresCss();	
		$this->appendCompiladoresJs();	
	}
	function appendCompiladoresCss(){
		require_once("lessc.php");
		require_once("MagicDefaultCssCompiladorDecorator.php");
		require_once("MagicDefaultCssCompiladorGrupoDecorator.php");
		require_once("MagicDefaultCssCompiladorMinificacaoDecorator.php");
		
	
		$this->LinkManager->addCompiladorMinificacaoDecorator(new MagicDefaultCssCompiladorMinificacaoDecorator());
		$this->LinkManager->addCompiladorUnidadeDecorator(new MagicDefaultCssCompiladorDecorator());
		$this->LinkManager->addCompiladorGrupoDecorator(new MagicDefaultCssCompiladorGrupoDecorator());
	}
	function appendCompiladoresJs(){
		require_once("jsMinify.php");
		require_once("MagicDefaultJsCompiladorMinificacaoDecorator.php");
		$jsMinificador = new MagicDefaultJsCompiladorMinificacaoDecorator();
		$this->BottomScriptManager->addCompiladorMinificacaoDecorator($jsMinificador);
		$this->TopScriptManager->addCompiladorMinificacaoDecorator($jsMinificador);
	}

}
?>
