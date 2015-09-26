<?php 
namespace Magic\Plugins\MagicCompiladoresDefault;
use Magic\Engine\Compilador\AbstractCompiladorDecorator;
/**
* Compilador padrão de unidade css do Magic
*/
class MagicDefaultCssCompiladorMinificacaoDecorator extends AbstractCompiladorDecorator
{
	
	public function compilar($content)
	{
		// 	// Remove comments
		// $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

		// // Remove space after colons
		// $content = str_replace(': ', ':', $content);

		// // Remove whitespace
		// $content = str_replace(array("\r\n", "\r", "\n", "\t"), '', $content);

		// // Collapse adjacent spaces into a single space
		// $content = preg_replace(" {2,}", ' ',$content);

		// // Remove spaces that might still be left where we know they aren't needed
		// $content = str_replace(array('} '), '}', $content);
		// $content = str_replace(array('{ '), '{', $content);
		// $content = str_replace(array('; '), ';', $content);
		// $content = str_replace(array(', '), ',', $content);
		// $content = str_replace(array(' }'), '}', $content);
		// $content = str_replace(array(' {'), '{', $content);
		// $content = str_replace(array(' ;'), ';', $content);
		// $content = str_replace(array(' ,'), ',', $content);
		return $content;
	}
}
?>