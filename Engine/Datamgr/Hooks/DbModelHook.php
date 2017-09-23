<?php 
namespace Magic\Engine\Datamgr\Hooks;
use Magic\Engine\Hooks\AbstractHook;
use Magic\Engine\Datamgr\DbModel;
class DbModelHook extends AbstractHook
{
	protected $dbModel;
	public function setDbModel(DbModel &$dbModel){
		$this->dbModel = $dbModel;
	}
	public function getDbModel(){
		return $this->dbModel;
	}
	function action(Array &$params){}
}
?>