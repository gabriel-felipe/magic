<?php 
/**
* Compilador padrão de unidade css do Magic
*/
class MagicDefaultCssCompiladorGrupoDecorator extends AbstractCompiladorDecorator
{
	
	public function compilar($conteudo)
	{
		$less = new lessc;
		//Quebra ele um array de linhas
		return $less->compile($conteudo);
	}
}
?>