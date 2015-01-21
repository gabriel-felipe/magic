<?php 
namespace Magic\Engine\Form\Decorators;
class TagWrapperDecorator extends AbstractFormDecorator
{
	protected $tag;
	protected $attributes;
	function __construct($tag, array $attributes=array()){
		$this->tag = $tag;
		$this->attributes = $attributes;

	}
	function compilar($conteudo){
		$attributes = "";
		foreach ($this->attributes as $key => $value) {
			if (preg_match_all("/\[([^]]+)\]/", $value,$matches)) {
				if (array_key_exists(1, $matches)) {
					foreach ($matches[1] as $attr) {
						$value = str_replace("[$attr]",$this->element->getAttr($attr),$value);
					}
				}
			}
			$attributes .= "$key = '".$value."' ";
			
		}
		return "<".$this->tag." $attributes>".$conteudo."</".$this->tag.">";

	}
}
?>