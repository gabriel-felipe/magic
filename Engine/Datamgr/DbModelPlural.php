<?php
namespace Magic\Engine\Datamgr;
use Magic\Engine\Datamgr\Sql\AbstractDbSelect;
use Magic\Engine\Hooks\HookChainManager;
use Magic\Engine\Hooks\AbstractHook;
class dbModelPlural {
	
	public $dbSelect;
	public $dbDelete;
	protected $single;
	public $plural = array();
	protected $dbModelHooks;
	public $lastSelect;

	public function __construct(DbModel $dbModel,$qtnByPage=999999999999) {
		$dbSelect = $dbModel->dbSelect;
		$this->dbSelect = $dbSelect;
		$this->lastSelect = $dbSelect;
		$this->dbDelete = $dbModel->dbDelete;

		$this->dbSelect->setPage(1);
		$this->dbSelect->setQtnByPage($qtnByPage);

		$this->single = $dbModel;
		$this->dbModelHooks = new HookChainManager;
		$this->dbModelHooks->setGlobalParams(array($this));
	}
	public function setPage($page=false){
		$this->dbSelect->setPage($page);
	}
	public function registerHook(AbstractHook $hook, $chainName){
		$this->dbModelHooks->registerHook($hook,$chainName);
	}
	protected function runSelect(AbstractDbSelect $select){
		$this->dbModelHooks->callChain("beforeSelect");
		$this->plural = array();
		$resultados = $select->run();
		if($resultados[0]){
			foreach ($resultados[0] as $key=>$atributos){
				$single = clone $this->single;
				$single->setData($atributos,1);
				$this->plural[$single->getId()] = $single;

			}

		}
		$this->lastSelect = $select;
		$this->dbModelHooks->callChain("afterSelect");

	}


	public function all($page=1){
		$select = clone $this->dbSelect;
		$this->runSelect($select);
		return $this->info();
	}
	public function last($n=1){
		$select = clone $this->dbSelect;
		$select->reverseOrder();
		$this->runSelect($select);
		return $this->info();
	}
	public function getByIds($ids=array(), $keep_order=false){
		if(is_array($ids)){
			$select = clone $this->dbSelect;
			foreach ($ids as $key => $id) {
				$select->addWhere($this->single->getPkField()." = :id".$key,array("id".$key=>$id),"or");
			}
			$this->runSelect($select);
			if($keep_order){
				$temp = array();
				$jaFoi = array();
				foreach($ids as $k=>$v){
					if(!in_array($v, $jaFoi)){
						foreach($this->plural as $produto){
							if($produto->id == $v){
								$temp[] = $produto;
								$jaFoi[] = $v;
								break;
							}
						}
					}
				}
				$this->plural = $temp;
				unset($temp);
			}
			return true;
		} else {
			throw new Exception("Error, function get_by_ids in dbuserplural class requires an array.", 1);
			
		}
	}
	
	public function destroy(){
		if ($this->plural) {
		
			$dbDelete = clone $this->dbDelete;
			$o = 0;
			foreach($this->plural as $obj){
				$key = "id".$o;
				$dbDelete->addWhere($this->single->getPkField()." = :$key",array($key=>$obj->getId()),"or");
				$o++;
			}
			return $dbDelete->run();
			# code...
		} else {
			return null;
		}
	}


	public function where($query,$array=array()){
		$select = clone $this->dbSelect;
		$select->addWhere($query,$array);
		$this->runSelect($select);
		return $this->info();
	}
	public function info(){
		$info = array();
		foreach($this->plural as $obj){
			$info[$obj->getId()] = $obj->info();
		}
		return $info;
	}
	public function order($coluna, $mode){
		$arrayOrder = array();
		$c = 0;
		foreach($this->plural as $key=>$item){
			$values = $item->parseInfo();
			$arrayOrder[$key] = $values[$coluna];
			$c++;
		}
		if($mode == "asc"){
			asort($arrayOrder);
		} else {
			arsort($arrayOrder);
		}

		$strPos = "";
		foreach($arrayOrder as $key=>$item){
			$strPos .= $key."-";
		}
		$strPos = substr($strPos, 0, -1);
		$arrayPos = explode("-",$strPos);
		$novoArray = array();
		foreach($this->plural as $key=>$item){
			$novoArray[$key] = $this->plural[$arrayPos[$key]];
		}
		$this->plural = $novoArray;
	}


}
?>