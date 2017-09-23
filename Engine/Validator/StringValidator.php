<?php 
namespace Magic\Engine\Validator;
class StringValidator extends AbstractValidator
{
	protected $errorMsg="O campo precisa ter entre :min e :max caracteres, porém possui :lenght caracteres.";
	public $min,$max;
	function __construct($min=0,$max=false){
		$this->min = $min;
		$this->max = $max;
	}
	function validate($string)
	{
		if (!is_string($string)) {
			$this->setErrorMsg("É esperada uma string para ser validada");
			return false;
		} else {
			$this->setErrorMsg("O campo precisa ter entre :min e :max caracteres, porém possui :lenght caracteres.");
			$lenght = mb_strlen($string, 'UTF-8');
			if (($lenght >= $this->min or !$this->min) and ($lenght <= $this->max or !$this->max)) {
				return true;
			} else {
				return false;
			}
		}
	}

	function getErrorParams($string){
		$lenght = mb_strlen($string, 'UTF-8');
		return array(":string"=>$string,":min"=>$this->min,":max"=>$this->max,":lenght"=>$lenght);
	}
}
?>