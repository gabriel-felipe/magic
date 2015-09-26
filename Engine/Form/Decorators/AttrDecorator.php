<?php 
namespace Magic\Engine\Form\Decorators;
class AttrDecorator extends AbstractFormDecorator
{
	protected $attributes;
	function __construct(array $attributes=array()){
		$this->attributes = $attributes;
	}
	function compilar($content){		
		
		
		return $content;

	}
	function setElement($element){
		parent::setElement($element);
		$element = $this->getElement();
		foreach ($this->attributes as $key => $value) {
			if (preg_match_all("/\[([^]]+)\]/", $value,$matches)) {
				if (array_key_exists(1, $matches)) {
					foreach ($matches[1] as $attr) {
						$value = str_replace("[$attr]",$this->element->getAttr($attr),$value);
					}
				}
			}
			$element->setAttr($key,$value);
			
		}

	}
}
?>