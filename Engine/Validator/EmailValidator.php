<?php 
namespace Magic\Engine\Validator;
class EmailValidator extends AbstractValidator
{
	protected $errorMsg="O e-mail fornecido (:email) não é um e-mail válido.";
	function validate($email)
	{
		return preg_match("/^.+@.+\..+/",$email) ? $email : false;
	}

	function getErrorParams($email){
		return array(":email"=>$email);
	}
}
?>