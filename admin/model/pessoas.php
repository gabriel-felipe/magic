<?php
require_once(path_datamgr."/dbmodel.php");
class pessoa extends dbModel{

	public function __construct($fields=false,$query=false,$queryParams=false)
	{

		parent::__construct("pessoas", $fields=false,$query=false,$queryParams=false);				
		$this->add_sanitize(array('nome', 'sobrenome', 'cargo'),'alpha_numeric');
		$this->add_sanitize('sexo', 'alpha');
		$this->add_sanitize(array('cpf','rg'), 'int') ;
		$this->add_sanitize('avatar','url');

		$this->add_validate(array('nome', 'sobrenome'),array('alpha_numeric'));
		$this->add_validate('sexo', array('alpha'));
	}
}
?>
