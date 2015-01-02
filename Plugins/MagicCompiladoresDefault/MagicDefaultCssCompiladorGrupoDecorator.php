<?php 
namespace Magic\Plugins\MagicCompiladoresDefault;
use Magic\Engine\Compilador\AbstractCompiladorDecorator;
/**
* Compilador padrão de unidade css do Magic
*/
class MagicDefaultCssCompiladorGrupoDecorator extends AbstractCompiladorDecorator
{
	
	public function compilar($conteudo)
	{
		require "lessc.php";
		$less = new \lessc;
		//Quebra ele um array de linhas
		return $less->compile($conteudo);
	}
}
?>