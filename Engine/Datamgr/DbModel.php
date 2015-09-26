<?php
namespace Magic\Engine\Datamgr;
use Magic\Engine\Datamgr\Sql\AbstractDbSelect;
use Magic\Engine\Datamgr\Driver\DbDriverFactory;
use Magic\Engine\Datamgr\DbManager;
use Magic\Engine\Hooks\HookChainManager;
use Magic\Engine\Hooks\AbstractHook;
use Magic\Engine\Datamgr\Hooks\DbModelHook;


use \Exception;
use \sanitize;
use \validate;

//Classe para model padrão para manipulação de bancos de dados.
class DbModel {
/*
Classe criada e distríbuida por Gabriel Felipe, qualquer dúvida mandar email para email@gabrielfelipe.com
Essa classe pode ser utilizada para uso comercial ou pessoal, desde que esses comentários de fonte sejam mantidos.
*/
/* 
 Definição de atributos e construção da classe.
*/
	public $dbSelect;
	public $dbInsert;
	public $dbUpdate;
	public $dbDelete;
	protected $data=array();
	protected $defaultData=array();
	protected $pkField=false;
	protected $dbModelHooks;

	public function __construct(AbstractDbManager $dbmanager,$table,$pkField=false,$fields="*",$driver=false) {
		$dbSelect = DbDriverFactory::getDbSelect($dbmanager, $table,$fields);
		$this->dbSelect = $dbSelect;
		$this->dbInsert = DbDriverFactory::getDbInsert($dbmanager, $table,$fields,$driver);
		$this->dbDelete = DbDriverFactory::getDbDelete($dbmanager, $table,$driver);
		$this->dbUpdate = DbDriverFactory::getDbUpdate($dbmanager, $table,$fields,$driver);
		$this->dbSelect->setPage(1);
		$this->dbSelect->setQtnByPage(1);

		if (!$pkField) {
			$pkField = $this->getDefaultPkField();
		}
		$this->setPkField($pkField);
		$this->dbSelect->addOrderBy($this->getPkField());
		$this->dbModelHooks = new HookChainManager;
		$this->dbModelHooks->setGlobalParams(array($this));
	}

	public function setPkField($pkField){
		$this->pkField = $pkField;

	}

	public function getDefaultData(){
		return $this->defaultData;
	}

	public function getDefaultPkField(){
		return $this->dbSelect->getTable().".".$this->dbSelect->getTable()."_id";
	}

	public function getPkField($columnOnly=0){
		if ($columnOnly) {
			$pk = explode(".",$this->pkField);
			return end($pk);
		} else {
			return $this->pkField;	
		}
	}


	//Funções de Pesquisa
	public function find($id){
		$select = clone $this->dbSelect;
		$select->addWhere($this->getPkField()." = :id",array("id"=>$id));
		return $this->runSelect($select);
	}

	public function where($where,$params){
		$select = clone $this->dbSelect;
		$select->addWhere($where,$params);
		return $this->runSelect($select);
	}

	public function last(){
		$select = clone $this->dbSelect;
		$select->reverseOrder();
		
		return $this->runSelect($select);
	}

	public function first(){
		$select = clone $this->dbSelect;	
		return $this->runSelect($select);
	}

	public function rand(){
		$select = clone $this->dbSelect;
		$select->setRandOrder();
		return $this->runSelect($select);
	}

	public function runSelect(AbstractDbSelect $select){

		$this->dbModelHooks->callChain("beforeSelect");
		$result = $select->run();
		$this->dbModelHooks->callChain("afterSelect");
		if ($result[1] > 0) {
			$this->setData($result[0][0],1);
			return true;
		} else {
			return false;
		}
	}

	public function info(){
		$parsedData = $this->dbModelHooks->callChain("parseInfo");
		return $this->data;
	}
	public function getData(){
		return $this->data;
	}

	public function setData(array $array,$updateDefaultData=0){
		$this->data = $array;
		if ($updateDefaultData) {
			$this->defaultData = $array;
		}
		
		return true;
	}

	public function save(){
		try {
			$this->dbModelHooks->callChain("beforeSave");
			if ($this->getId()) {
				// UPDATE
				$updated = array_diff_assoc($this->data, $this->defaultData);
				if ($updated) {
					$this->dbModelHooks->callChain("beforeUpdate");
					$dbUpdate = clone $this->dbUpdate;
					$dbUpdate->setFields(array_keys($updated));
					$dbUpdate->setData($updated,1);
					$dbUpdate->addWhere($this->getPkField()." = :id",array("id"=>$this->getId()));
					$result = $dbUpdate->run();
					$this->dbModelHooks->callChain("afterUpdate");
					return $result;
				} else {
					return true; //Nothing to update
				}
				
			} else {
				$this->dbModelHooks->callChain("beforeNew");
				// INSERT
				$data = $this->getData();
				$dbInsert = clone $this->dbInsert;
				$dbInsert->setFields(array_keys($data));
				$dbInsert->setData($data,1);
				$result = $dbInsert->run();
				$dbSelect = clone $this->dbSelect;
				$dbSelect->setFields($this->getPkField()." as dbModelPkField");
				$dbSelect->setOrderBy(array(array("column"=>$this->getPkField(),"mode"=>"DESC")));
				$dbSelect->setQtnByPage(1);
				$result = $dbSelect->run();
				$this->{$this->getPkField(1)} = $result[0][0]["dbModelPkField"];
				$this->dbModelHooks->callChain("afterNew");
				return $result;

			}
			$this->dbModelHooks->callChain("afterSave");
		} catch (Exception $e){ 
			throw $e;	
		}

	}
	public function destroy(){
		$this->dbModelHooks->callChain("beforeDestroy");
		$dbDelete = clone $this->dbDelete;
		$dbDelete->addWhere($this->getPkField()." = :id",array("id"=>$this->getId()));
		$result = $dbDelete->run();
		$this->dbModelHooks->callChain("afterDestroy");
		return $result;
	}
	public function getId(){
		return isset($this->data[$this->getPkField(1)]) ? $this->data[$this->getPkField(1)] : "";
	}

	public function registerHook(DbModelHook $hook, $chainName){
		$hook->setDbModel($this);
		$this->dbModelHooks->registerHook($hook,$chainName);
	}
	
/*
Funções de pesquisa
*/

	
	///MAGIC METHODS
	public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    public function __get($name){
    	return (isset($this->data[$name])) ? $this->data[$name] : false;
    }


}
?>