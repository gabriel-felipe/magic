<?php 
namespace Magic\Engine\Validator;
use \Magic\Engine\Datamgr\Driver\DbDriverFactory;
use \Magic\Engine\Datamgr\AbstractDbManager;
class NoRecordExistsValidator extends AbstractValidator
{
	protected $errorMsg="Já existe um registro com esse :valor na coluna :coluna da tabela :tabela";
	protected $tabela,$coluna;
	public $dbSelect;
	function __construct($tabela,$coluna,AbstractDbManager $dbManager){
		$this->coluna = $coluna;
		$this->tabela = $tabela;
		$this->dbSelect = DbDriverFactory::getDbSelect($dbManager,$tabela,array($coluna));
	}
	function validate($valor)
	{
		$dbSelect = clone $this->dbSelect;
		$dbSelect->addWhere($this->coluna." = :valor",array("valor"=>$valor));
		$result = $dbSelect->run();
		if ($result[1] > 0) {
			return false;
		} else {
			return true;
		}
	}
	function getDbSelect(){
		return $this->dbSelect;
	}

	function getErrorParams($valor){
		return array(":valor"=>$valor,":coluna"=>$this->coluna,":tabela"=>$this->tabela);
	}
}
?>