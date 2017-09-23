<?php 
namespace Magic\Engine\Validator;
class EmailValidator extends AbstractValidator
{
	protected $errorMsg="Insira um e-mail válido.";
	function validate($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	function getErrorParams($email){
		return array(":email"=>$email);
	}
}
?>