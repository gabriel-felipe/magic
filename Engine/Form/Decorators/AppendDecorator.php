<?php 
namespace Magic\Engine\Form\Decorators;
class AppendDecorator extends AbstractFormDecorator
{
	protected $content;
	protected $atStart=false;
	function __construct($content,$atStart=false){
		$this->content = $content;
		$this->atStart = $atStart;

	}
	function compilar($conteudo){
		$content = $this->content;
		if (preg_match_all("/\[([^]]+)\]/", $content,$matches)) {
			if (array_key_exists(1, $matches)) {
				foreach ($matches[1] as $attr) {
					$content = str_replace("[$attr]",$this->element->getAttr($attr),$content);
				}
			}
		}
		return ($this->atStart) ? $content.$conteudo : $conteudo.$content;
	}
}
?>