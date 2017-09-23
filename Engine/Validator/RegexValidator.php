<?php 
namespace Magic\Engine\Validator;
class RegexValidator extends AbstractValidator
{
	protected $errorMsg="O valor fornecido(:valor) não é compatível com o padrão (:regex).";
	protected $regex;
	function __construct($regex,$error=false){
		$this->regex = $regex;
		if ($error) {
			$this->errorMsg = $error;
		}
	}
	function validate($valor)
	{
		return preg_match($this->regex,$valor) ? $valor : false;
	}

	function getErrorParams($valor){
		return array(":valor"=>$valor,":regex"=>$this->regex);
	}
}
?>