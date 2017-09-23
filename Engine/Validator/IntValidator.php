<?php 
namespace Magic\Engine\Validator;
class IntValidator extends AbstractValidator
{
	protected $errorMsg="Por favor forneça um número inteiro.";
	protected $positiveOnly;
	function __construct($positiveOnly=false){
		$this->positiveOnly = $positiveOnly;
	}
	function validate($input)
	{
		if ($input[0] == '-' and !$this->positiveOnly) {
			return ctype_digit(substr($input, 1));
		}
		return ctype_digit($input);
	}
	function getErrorParams($file){
		return array();
	}
}
?>