<?php 
/**
* Compilador padrão de unidade css do Magic
*/
class MagicDefaultCssCompiladorDecorator extends AbstractCompiladorDecorator
{
	
	public function compilar($conteudo)
	{
		//Quebra ele um array de linhas
		$linhas = explode("\n",str_replace("\n\r", "\n", $conteudo));
		//Procura uma série de padrões e convenções e faz as alterações.
		foreach($linhas as $l=>$rule){

			if(preg_match("/^[\s]*if(.+):$/",$rule,$match)){
				$linhas[$l] = str_replace($match[0], "<?php if(".$match[1].") { ?>",$rule);	
			}
			if(preg_match("/^[ \t\s]*end[ \t\s]*$/",$rule,$match)){
				$linhas[$l] = str_replace("end", "<?php } ?>",$linhas[$l]);
			}
			if(preg_match("/else:/",$rule,$match)){
				$linhas[$l] = str_replace("else:", "<?php } else { ?>",$linhas[$l]);
			}
			if(preg_match("/elseif  *(.+) *:/",$rule,$match)){
				$linhas[$l] = str_replace($match[0], "<?php } elseif(".$match[1].") { ?>",$rule);
			}
		}
		//Junta todas as linhas numa única string novamente
		$conteudo = implode("\n",$linhas);
        return $conteudo;
	}
}
?>