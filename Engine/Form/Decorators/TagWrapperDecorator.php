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
			$attributes .= "$key = '".$value."'";
		}
		return "<".$this->tag." $attributes>".$conteudo."</".$this->tag.">";

	}
}
?>