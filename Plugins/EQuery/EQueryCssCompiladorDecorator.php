<?php 
namespace Magic\Plugins\EQuery;
use Magic\Engine\Compilador\AbstractCompiladorDecorator;
/**
* Compilador de css para permitir função equery
*/
class EQueryCssCompiladorDecorator extends AbstractCompiladorDecorator
{
	
	public function compilar($conteudo)
	{
		global $registry;
		$EQueryPlugin = $registry->get("EQuery");
		//Quebra ele um array de linhas
		$linhas = explode("\n",str_replace("\n\r", "\n", $conteudo));
		foreach($linhas as $l=>$rule){
			if(preg_match("/([^\s]+)@eq\(([^)]+)\)/",$rule,$match)){
				$element = $match[1];
				$expression = str_replace(" ","_",strtolower($match[2]));
				$linhas[$l] = str_replace($match[0],"$element.$expression",$linhas[$l]);
				
				$EQueryPlugin->addEquery($element,$match[2]);
			}
		}
		$conteudo = implode("\n",$linhas);
		return $conteudo;
	}
}
?>