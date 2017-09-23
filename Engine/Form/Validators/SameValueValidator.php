<?php namespace Magic\Engine\Form\Validators;
use Magic\Engine\Validator\AbstractValidator;
use Magic\Engine\Form\AbstractElement;

class SameValueValidator extends AbstractValidator {
	protected $element;
	protected $errorMsg="O valor não confere com o campo :campo";
	protected $pathRoot;
	function __construct(AbstractElement $element,$error=false){
		$this->element = $element;
		if ($error) {
			$this->errorMsg = $error;
		}
	}
	function validate($valor)
	{
		return $valor === $this->element->getValue();
	}

	function getErrorParams($file){
		return array(":campo"=>$this->element->getLabel());
	}
}
?>