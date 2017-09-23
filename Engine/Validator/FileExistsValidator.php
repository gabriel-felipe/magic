<?php 
namespace Magic\Engine\Validator;
class FileExistsValidator extends AbstractValidator
{
	protected $errorMsg="O arquivo fornecido não existe";
	protected $pathRoot;
	function __construct($pathRoot=false){
		$this->pathRoot = ($pathRoot) ? $pathRoot : path_root;
	}
	function validate($file)
	{
		return file_exists($this->pathRoot.$file);
	}

	function getErrorParams($file){
		return array(":file"=>$file);
	}
}
?>