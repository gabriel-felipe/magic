<?php 
namespace Magic\Plugins\MagicCompiladoresDefault;
use Magic\Engine\Compilador\AbstractCompiladorDecorator;
/**
* Compilador padrão de unidade css do Magic
*/
class MagicDefaultJsCompiladorMinificacaoDecorator extends AbstractCompiladorDecorator
{
	public function compilar($content)
	{
		return $content;
		// return Minifier::minify($content);
	}
}
?>