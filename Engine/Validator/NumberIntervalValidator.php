<?php 
namespace Magic\Engine\Validator;
class NumberIntervalValidator extends AbstractValidator
{
	protected $errorMsg="O número fornecido precisa ser entre :min e :max, porém é :valor.";
	public $min,$max;
	function __construct($min=0,$max=false){
		$this->min = $min;
		$this->max = $max;
	}
	function validate($valor)
	{
		if (!is_numeric($valor)) {
			$this->setErrorMsg("Por favor forneça um número.");
			return false;
		} else {
			$this->setErrorMsg("O número fornecido precisa ser entre :min e :max, porém é :valor.");
			if (($valor >= $this->min or !$this->min) and ($valor <= $this->max or !$this->max)) {
				return true;
			} else {
				return false;
			}
		}
	}

	function getErrorParams($valor){
		$valor = $valor;
		return array(":min"=>$this->min,":max"=>$this->max,":valor"=>$valor);
	}
}
?>