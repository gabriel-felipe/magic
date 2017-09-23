<?php 
namespace Magic\Engine\Validator;
class CallbackValidator extends AbstractValidator
{
	protected $errorMsg="O valor fornecido (:valor) não é válido";
	protected $callback;
	function __construct($callback,$error=false){
		$this->setCallback($callback);
		if ($error) {
			$this->errorMsg = $error;
		}
	}
	public function setCallback($callback){
		if (is_callable($callback)) {
			$this->callback = $callback;
			return $this;
		}
		throw new Exception("Callback must be callable.", 1);
	}

	function validate($valor)
	{
		return call_user_func_array($this->callback,array($valor));
	}

	function getErrorParams($valor){
		return array(":valor"=>$valor);
	}
}
?>