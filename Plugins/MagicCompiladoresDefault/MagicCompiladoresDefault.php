<?php 
namespace Magic\Plugins\MagicCompiladoresDefault;
use Magic\Engine\Plugin\AbstractPlugin;
/**
* Plugin responsável por inserir os assets padrões do Magic ao documento.
*/
class MagicCompiladoresDefault extends AbstractPlugin
{
	protected $version = 1.0;
	protected $compatibleWith = array("1");
	function init()
	{
		$this->appendCompiladoresCss();	
		$this->appendCompiladoresJs();	
	}
	function appendCompiladoresCss(){
		
	
		$this->LinkManager->addCompiladorMinificacaoDecorator(new MagicDefaultCssCompiladorMinificacaoDecorator());
		$this->LinkManager->addCompiladorUnidadeDecorator(new MagicDefaultCssCompiladorDecorator());
		$this->LinkManager->addCompiladorGrupoDecorator(new MagicDefaultCssCompiladorGrupoDecorator());
	}
	function appendCompiladoresJs(){
		
		$jsMinificador = new MagicDefaultJsCompiladorMinificacaoDecorator();
		$this->BottomScriptManager->addCompiladorMinificacaoDecorator($jsMinificador);
		$this->TopScriptManager->addCompiladorMinificacaoDecorator($jsMinificador);
	}

}
?>
