<?php
namespace Magic\Engine\Datamgr;
use \Exception;
use \sanitize;
use \validate;

class dbModelPlural {
	
	protected $tabela;
	public $plural = array();
	protected $listaAtts;
	protected $dbmanager;
	public $qtnbypage;
	public $total;
	public $nowinpage;
	public $last_query;
	public $last_query_array;
	protected $fields;
	protected $registry;
	public $joins = array();
	public $addFields = array();
	protected $fieldsStr;
	public $single = "dbModel";
	public $groupBy = false;
	public $orderBy = false;
	public $orderMode = "ASC";
	protected $globalWhere = array(); //Array to fill with global conditions;


	public function __construct($table,$fields=false,$where=false,$array=array(),$plural=array(),$page=1,$qtnbypage=9999999999) {
		//Criando um objeto vazio
		$this->plural = array();
		$this->tabela = "`".$table."`";
		$this->pk_field = $table."_id";
		$this->orderBy = $this->tabela.".".$this->pk_field;

		//Criando um objeto vazio
		$this->plural = $plural;
		$this->qtnbypage = $qtnbypage;
		$db = new DbManager;
		$this->dbmanager = $db;
		$this->listaAtts = array();
		$colunas = $this->dbmanager->fetch_columns("`".$table."`");
		foreach($colunas as $coluna){
			if($coluna['name'] == $this->pk_field or !$fields or (is_array($fields) and in_array($coluna['name'], $fields)) or $fields == $coluna['name'] or $fields == "*" ){
				$this->listaAtts[$coluna['name']] = "";
			}
		}
		if($this->single != "dbModel"){
			$class = $this->single;
			$class = new $class;
			$this->joins = $class->getJoins();
			$this->addFields = $class->getAddFields();
			$this->groupBy = $class->groupBy;
			unset($class);
		}
		$this->update_fields();
		
		if($where){
			$this->where($where,$array,$page);
		} elseif(count($plural) > 0){
			$this->arrayToObject();
		}
		
	}
	public function getLastQuery(){
		return $this->dbmanager->lastQuery;
	}
	// public function get_select(){
	// 	$keys = array_keys($this->listaAtts);
	// 	return implode(", ",$keys);
	// }
	public function update_fields(){
		$this->fieldsStr = $this->get_select();
	}
	public function addField($alias,$sql){
		$this->addFields[$alias] = $sql;
		$this->fieldsStr = $this->get_select();
		return true;
	}
	public function addJoin($table,$fields=array(),$on="",$method="LEFT"){
		$on = ($on) ? $on : $table.".".$table."_id = [t].".$table."_id";
		$this->joins[$table] = array(
			"fields" => $fields,
			"on" => $on,
			"method" => $method
		);
		$this->fieldsStr = $this->get_select();

	}
	public function removeJoin($table){
		unset($this->joins[$table]);
		$this->fieldsStr = $this->get_select();

	}
	public function removeField($alias){
		unset($this->addFields[$alias]);
		$this->fieldsStr = $this->get_select();

	}
	public function get_select(){
		$keys = array_keys($this->listaAtts);
		foreach($keys as &$key){
			$key = $this->tabela.".$key";
		}
		$keys = implode(", ",$keys);
		$from = $this->get_from();
		$joinsObj = $this->joins;
		$addFieldsObj = $this->addFields;
		foreach ($joinsObj as $table => $join) {
			$on = str_replace("[t]",$this->tabela, $join['on']);
			foreach($join['fields'] as $field => $name){
				$keys .= ", $table.$field as $name ";
			}
		}
		foreach ($addFieldsObj as $value => $key) {
			$key = str_replace("[t]", $this->tabela, $key);
			$keys .= ", $key as $value";
		}

		return $keys.$from;
	}
	public function getGlobalWhere(){
		return $this->globalWhere();
	}
	public function globalWhereQuery($query,$data){
		foreach($this->globalWhere as $q => $values){
			$and = ($query) ? " and " : "";
			$query .= $and.$q;
			$data = array_merge($data,$values);
		}
		if ($query) {
			$query = " WHERE $query";
		}
		if ($this->groupBy) {
			$query .= " GROUP BY ".sanitize::no_special($this->groupBy);
		}
		$query .= " ORDER BY ".sanitize::no_special($this->orderBy)." ".sanitize::no_special($this->orderMode);

		return array($query,$data);
	}
	public function get_from(){
		$joins = "";
		$joinsObj = $this->joins;
		foreach ($joinsObj as $table => $join) {
			$on = str_replace("[t]",$this->tabela, $join['on']);
			$joins .= $join['method']." JOIN $table on ($on) ";
		}
		return " FROM ".$this->tabela." ".$joins;
	}
	
	
	protected function arrayToObject(){
		if(is_array($this->plural)){
			foreach ($this->plural as $key=>$atributos){
				$class = $this->single;
				
				if($class == 'dbModel'){
					$single = new dbModel($this->tabela);	
					$single->joins = $this->joins;
				} else {
					$single = new $class;
					$single->joins = $this->joins;
					$single->addFields = $this->addFields;
				}
				
				$single->setAtributos($atributos);
				$this->plural[$key] = $single;
			}
		}
	}

	public function get_element_list(){
		$array = array();
		foreach($this->plural as $obj){
			$array[] = $obj->id;
		}
		return $array;
	}
	public function all($page=1){
		$where = $this->globalWhereQuery("",array());
		$table = $this->tabela;
		$ini = ($page-1)*$this->qtnbypage;
		$fim = $this->qtnbypage;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]." LIMIT $ini,$fim",$where[1]);
		$qtntotal = $this->dbmanager->query("SELECT COUNT(*) ".$this->get_from().$where[0], $where[1]);
		$qtntotal = ($qtntotal[1] > 1) ? $qtntotal[1] : $qtntotal[0][0]["COUNT(*)"];
		$this->nowinpage = $page;
		$this->last_query = "SELECT {$this->fieldsStr} ".$this->get_from()." ".$this->tabela.$where[0];
		$this->total = $qtntotal;
		$this->plural = $resultados[0];
		$this->arrayToObject();
		$this->last_query = "SELECT {$this->fieldsStr} ".$this->get_from()." ".$this->tabela.$where[0]." LIMIT $ini,$fim";
		$this->last_query_array = array();
		return $this->info();
	}
	public function last($n=1){
		$this->orderBy = $this->pk_field;
		$this->orderMode = "DESC";
		$where = $this->globalWhereQuery("",array());
		$table = $this->tabela;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]." LIMIT $n",$where[1]);
		$this->plural = $resultados[0];
		$this->arrayToObject();
		$this->last_query = "SELECT {$this->fieldsStr} ".$where[0]." LIMIT $n ORDER BY {$this->pk_field} DESC";
		$this->last_query_array = array();
		return $this->info();
	}
	public function get_by_ids($ids=array(), $keep_order=false,$page=1){
		if(is_array($ids)){
			$i = 0;
			$values = array();
			$ids_temp = array();
			$ini = ($page-1)*$this->qtnbypage;
			$fim = $page + $this->qtnbypage - 1;
			$this->nowinpage = $page;
			$this->total = count($ids);
			foreach($ids as $k=>$v){
				if($i >= $ini){
					$param = "id".$v;
					$ids_temp[$v] = "{$this->pk_field} = :$param";
					$values[":$param"] = $v;	
					if($i == $fim){
						break;
					}
				}
				
				$i++;
			}
			$query = implode(" or ",$ids_temp);

			$this->where($query,$values);
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
	public function listar($before="",$template="",$after=""){
		echo $before;
		foreach($this->plural as $single){
		$single->exibir($template);
		}
		echo $after;
	}
	
	public function destroy(){
		foreach($this->plural as $obj){
			$obj->destroy();
		}
	}


	public function where($query,$array=array(),$page=1){
		$where = $this->globalWhereQuery($query,$array);
		$this->plural = array();
		$ini = ($page-1)*$this->qtnbypage;
		$fim = $this->qtnbypage;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]. " LIMIT $ini,$fim",$where[1]);
		$qtntotal = $this->dbmanager->query("SELECT COUNT(*) ".$this->get_from().$where[0], $where[1]);
		if (isset($qtntotal[0][0])) {
			$qtntotal = $qtntotal[0][0]["COUNT(*)"];
		} else {
			$qtntotal = 0;
		}
		
		$this->nowinpage = $page;
		$this->total = $qtntotal;
		
		$this->last_query = "SELECT {$this->fieldsStr} WHERE ".$where[0];
		$this->last_query_array=$array;
		$this->plural = $resultados[0];
		$this->arrayToObject();
		return $this->info();
	}
	public function info(){
		$info = array();
		foreach($this->plural as $obj){
			$info[$obj->id] = $obj->info();
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
	public function __set($name,$value){
		$this->$name = $value;
	}


}
?>