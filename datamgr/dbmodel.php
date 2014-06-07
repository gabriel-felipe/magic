<?php
//Conexão com o banco de dados
require_once(path_datamgr."/bff-dbmanager.php");


//Classe para model padrão para manipulação de bancos de dados.
class dbModel {
/*
Classe criada e distríbuida por Gabriel Felipe, qualquer dúvida mandar email para email@gabrielfelipe.com
Essa classe pode ser utilizada para uso comercial ou pessoal, desde que esses comentários de fonte sejam mantidos.
*/
/* 
 Definição de atributos e construção da classe.
*/
	protected $listaAtts;
	public $tabela;
	protected $array_has_one;
	protected $array_belongs_to;
	protected $array_has_many;
	protected $cb_destroy_has_many;
	protected $dbmanager;
	protected $parsedValues;
	protected $sanitizer;
	protected $sanitize = array();
	protected $sanitized = array();
	protected $validator;
	protected $validate = array();
	protected $validated = array();
	protected $fields = array();
	protected $pk_field;
	protected $joins = array();
	protected $addFields = array();
	protected $globalWhere = array(); //Array to fill with global conditions;
	protected $defaultValues = array(); //array to fill with default values after a find.
	protected $parsedAtts = array();
	
		public function __construct($tabela,$fields=false,$query=false,$queryParams=false) {
			if(!DB_ACTIVE){
				$backtrace = debug_backtrace(10);
				$backtrace = print_r($backtrace,true);
				throw new Exception("Database not configured, please configure it in /config/db.php. Backtrace of this call:
						\n
					".$backtrace, 1);
			}
			$this->pk_field = $tabela."_id";
			$this->{$this->pk_field} = 0;
			$this->id = &$this->{$this->pk_field};
			$db = new bffdbmanager;
			$this->dbmanager = $db;
			
			
			$this->listaAtts = array();
			$colunas = $this->dbmanager->fetch_columns($tabela);
			foreach($colunas as $coluna){
				if($coluna['name'] == $this->pk_field or !$fields or (is_array($fields) and in_array($coluna['name'], $fields)) or $fields == $coluna['name'] or $fields == "*" ){
					$this->listaAtts[$coluna['name']] = "";
				}
			}
			$this->setAtributos();
			$this->tabela = $tabela;
			$this->sanitizer = new sanitize;
			$this->validator = new validate;
			if(is_int($query) and $query > 0){
				$this->find($query);
			} elseif(is_string($query) and $query and is_array($queryParams)){
				$this->where($query,$queryParams);
			}

		}

	protected function isSelected($error,$triggerException=true){
		if($this->id){
			return true;
		} else {
			if($triggerException){
				throw new Exception($error, 1);
			}
			return false;
		}
	}
	public function getAtts(){
		$atts = $this->listaAtts;
		$joinAtts = array();
		foreach($this->joins as $table => $join){
			foreach($join['fields'] as $field => $name){
				$joinAtts[$name] = "";
			}
		}
		foreach($this->addFields as $field => $query){
			$joinAtts[$field] = "";
		}
		foreach($this->parsedAtts as $att){
			$joinAtts[$att] = "";
		}
		return array_merge($atts,$joinAtts);
	}
	public function setAtributos($args=false){
		if(!$args){
			foreach($this->getAtts() as $att => $value) {
			$this->$att = $value;
			}	
		} else {
			foreach($args as $att => $value) {
				$this->{$att} = $value;
			}	
		}
		$this->updateRelationships();		
	}
/* 
Funções de manipulações de dados.
exibir(template) = função que exibe as informações dos objetos. ela substitui as ocorrências de '[nomedoatributo]' por 'valor setado no atributo atual'
Ex: exibir("-[nome]") vai retornar echo "-".$this->nome

find(id) = preenche os dados dos atributos com os dados do registro que comporta aquela id.

save() = se tiver um id definido, atualiza os dados. Do contrário, cria um novo registro.

destroy() = deleta o registro da tabela.

*/
	public function parseInfo(){
		$this->before_parseInfo();
		foreach($this->getAtts() as $att=>$val){
			if(method_exists($this, "parse_$att")){
				$method = "parse_$att";
				$this->parsedValues[$att] = $this->$method();
			} else{
				$this->parsedValues[$att] = $this->$att;
			}
		}

		return $this->parsedValues;
	}
	public function sanitizeData(){
		foreach($this->listaAtts as $att=>$val){
			if(isset($this->sanitize[$att])){
				if(method_exists($this->sanitizer, $this->sanitize[$att])){
					$method = $this->sanitize[$att];
					$this->sanitized[$att] = $this->sanitizer->$method($this->$att);
				} else {
					$method = $this->sanitize[$att];
					throw new Exception("Erro ao limpar os dados, método de limpeza $method não existe =(", 1);
				}
			} else{
				$this->sanitized[$att] = $this->$att;
			}
		}
		return $this->sanitized;
	}
	public function add_sanitize($obj, $info){
		if(is_array($obj)){
			foreach($obj as $o){
				$this->add_sanitize($o,$info);
			}
		} else {
			$this->sanitize[$obj] = $info;
		}
	}
	public function add_validate($obj, $info){
		if(is_array($obj)){
			foreach($obj as $o){
				$this->add_validate($o,$info);
			}
		} else {
			$this->validate[$obj] = $info;
		}
	}
	public function validateData(){
		$errors = array();
		foreach($this->listaAtts as $att=>$val){
			if(isset($this->validate[$att]) and is_array($this->validate[$att])){
				$info = $this->validate[$att];
				if(method_exists($this->validator, $info[0])){
					$method = $info[0];
					if(!array_key_exists(1, $info) or !is_array($info[1])){
						$info[1] = array();
					}
					$params = array_unshift($info[1], $this->sanitized[$att]);
					$valida = call_user_func_array(array($this->validator,$method), $info[1]);
					if(!$valida){
						$valor = $this->sanitized[$att];
						$errors[] = "Erro ao validar $att, valor($valor) não passou na validação para $method";
					}
				} else {
					$method = $this->validate[$att][0];
					throw new Exception("Erro ao validar $att, método de validação $method não existe =(", 1);
				}
			} else{
				$this->validated[$att] = $this->sanitized[$att];
			}
		}

		if(count($errors) > 0){
			$errors = implode("\n<br />", $errors);
			throw new Exception("Erros na validação: \n $errors", 1);
		} else {
			return true;
		}
	}
	public function exibir($template=""){
		$this->parseInfo();
		$htmlRoot = $_SESSION['htmlRoot'];
		$id = $this->id;
			foreach($this->listaAtts as $att => $value) {
				$value = (isset($this->parsedValues[$att])) ? $this->parsedValues[$att] : $value;
				$template = str_replace("[$att]", $value, $template);
			}
		return $template;
	}
	public function before_parseInfo(){
		
	}
	public function before_save(){
		try {
			$this->before_new_n_update();
		} catch(Exception $e){
			throw $e;
			
		}
		if($this->id == ""){
			try {
				$this->before_new();
			} catch(Exception $e){
				throw $e;
			}
		} else {
			try {
				$this->before_update();
			} catch(Exception $e){
				throw $e;
			}
		}
		try {
			$this->sanitizeData();
			$this->validateData();
		} catch(Exception $e){
			throw $e;
		}
	}
	public function before_new_n_update(){
		return true;
	}
	public function before_new(){
		return true;
	}
	public function before_info(){

	}
	public function before_update(){

	}
	public function before_destroy(){

	}
	public function after_new_n_update(){
	}

	public function after_update(){
		
	}
	public function after_save(){
		if($this->id == ""){
			try {
				$this->after_new();
			} catch(Exception $e){
				/*throw $e;*/
			}
		} else {
			try {
				$this->after_update();
			} catch(Exception $e){
				/*throw $e;*/
			}
		}
		try {
			$this->after_new_n_update();
		} catch(Exception $e){
			/*die($e->getMessage());*/
		}

	}
	public function after_new(){
		try {
			$this->last();
		} catch (Exception $e){
			/*die($e->getMessage);*/
		}
	}
	public function after_destroy(){
	}

	public function after_setAtributos(){

	}

	public function save(){
		
		try {
			
			$this->before_save();
			$listaCampos = "";
			$listaValores = "";
			$valores = array();
			
			foreach($this->listaAtts as $att => $value){
				$valores[":$att"] = $this->sanitized[$att];
			}
			if($this->id == ""){
				foreach($this->listaAtts as $att => $value){
					$listaCampos .= $att.",";
					$listaValores .= ":$att,";
				}
				$listaCampos = substr($listaCampos, 0, -1);
				$listaValores = substr($listaValores,0, -1);
				$query = "insert into ".$this->tabela."($listaCampos) VALUES ($listaValores)";
			} else 
			{
				unset($valores[':id']);
				$this->before_update();
				$query = "UPDATE ".$this->tabela." set ";
				foreach($this->listaAtts as $att => $value){
					if($att != "id"){
						$query .= "$att = :$att, ";
					}
				}
				$query = substr($query, 0, -2);
				$query .= " WHERE {$this->pk_field} = ".$this->id."";
			}

			try{
				
				$this->dbmanager->query($query, $valores);
				try {
					$this->after_save();
					return true;
				} catch(Exception $e){
					/*die($e->getMessage);*/
				}
			} catch(Exception $e)
			{	
				$erro = $e->getMessage();
				die(<<<EOD
				Erro ao executar a Query $query <br />
				Msg Erro: $erro.
				Por favor, tente novamente ou contate o administrador do sistema.	
EOD
				);
				return false;
			}
		} catch(Exception $e) {
			throw $e;
			
		}

	}
	public function destroy(){
		
		
			$this->before_destroy();
			$id = $this->id;

			$query = "DELETE FROM ".$this->tabela." where {$this->pk_field}=:id";
			if($this->dbmanager->query($query, array(":id" => $id))){
				$info = $this->infoString();
				$this->after_destroy();
				return true;
			} else 
			{	
				$info = $this->infoString();
				return false;
			}
		
	}	
	
/*
Funções de pesquisa
*/

	public function addField($alias,$sql){
		$this->addFields[$alias] = $sql;
		return true;
	}
	public function addJoin($table,$fields=array(),$on=""){
		$on = ($on) ? $on : $table.".".$table."_id = [t].".$table."_id";
		$this->joins[$table] = array(
			"fields" => $fields,
			"on" => $on
		);
	}
	public function removeJoin($table){
		unset($this->joins[$table]);
	}
	public function removeField($alias){
		unset($this->addFields[$alias]);
	}
	public function get_select(){
		$keys = array_keys($this->listaAtts);
		foreach($keys as &$key){
			$key = $this->tabela.".$key";
		}
		$keys = implode(", ",$keys);
		$from = " FROM ".$this->tabela." ";
		$joins = "";
		foreach ($this->joins as $table => $join) {
			$on = str_replace("[t]",$this->tabela, $join['on']);
			$joins .= "LEFT JOIN $table on ($on) ";
			foreach($join['fields'] as $field => $name){
				$keys .= ", $table.$field as $name ";
			}
		}
		foreach($this->addFields as $field => $query){
			$query = str_replace("[t]",$this->tabela, $query);
			$keys .= ", $query as $field ";
		}
		return $keys.$from.$joins;
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
		return array($query,$data);
	}
	public function find($id) {
		$where = $this->globalWhereQuery("{$this->tabela}.{$this->pk_field}=:id",array(":id"=>$id));
		
		$this->defaultValues = array();
		$fields = $this->get_select();
		$q = "SELECT $fields".$where[0];
		$buscaSingle = $this->dbmanager->query($q,$where[1]);
		if($buscaSingle[1] == 1){

			$this->setAtributos($buscaSingle[0][0]);
			foreach($buscaSingle[0][0] as $key=>$val){
				$this->defaultValues[$key] = $val;
			}
			return true;
		} else 
		{
			$this->setAtributos();
			return false;
		}
	}

	public function last() {
		$this->defaultValues = array();
		$fields = $this->get_select();
		$query = "SELECT $fields ORDER by {$this->tabela}.{$this->pk_field} DESC LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query);
		if($buscaSingle[1] == 1){
			$this->setAtributos($buscaSingle[0][0]);
			foreach($buscaSingle[0][0] as $key=>$val){
				$this->defaultValues[$key] = $val;
			}
		} else 
		{
			echo "<h1>Ocorreu um erro, me desculpe<h1>";
		}
	}
	public function rand(){
		$this->defaultValues = array();
		$fields = $this->get_select();
		$query = "SELECT $fields ORDER by RAND() LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query);
		if($buscaSingle[1] == 1){
			$this->setAtributos($buscaSingle[0][0]);
			foreach($buscaSingle[0][0] as $key=>$val){
				$this->defaultValues[$key] = $val;
			}
		} else 
		{
			echo "<h1>Ocorreu um erro, me desculpe<h1>";
		}
	}
	public function first() {
		$this->defaultValues = array();
		$fields = $this->get_select();
		$query = "SELECT $fields ORDER by {$this->pk_field} ASC LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query);
		if($buscaSingle[1] == 1){
			$this->setAtributos($buscaSingle[0][0]);
			foreach($buscaSingle[0][0] as $key=>$val){
				$this->defaultValues[$key] = $val;
			}
		} else 
		{
			echo "<h1>Ocorreu um erro, me desculpe<h1>";
		}
	}
	public function where($query,$array=array()){
		$this->defaultValues = array();
		$fields = $this->get_select();
		
		$q = "SELECT $fields WHERE $query LIMIT 1";
		$buscaSingle = $this->dbmanager->query($q, $array);
		if($buscaSingle[1] == 1){
			$this->setAtributos($buscaSingle[0][0]);
			foreach($buscaSingle[0][0] as $key=>$val){
				$this->defaultValues[$key] = $val;
			}
			return $this->info();
		} else {
			return false;
		}
	}
/*
Retornar só as infos dos atributos
*/
	public function info(){
		$this->before_info();
		$info = array();
			foreach($this->getAtts() as $att => $value) {
				if (isset($this->$att)) {
					$info[$att] = $this->$att;
				} else {
					$info[$att] = "";
				}
			}	
		return $info;
	}
	public function infoString(){
		$info = "";
		foreach($this->info() as $key => $value){
			$value = print_r($value,true);
			$info .= "$key : $value | ";
		}
		return $info;
	}
/*
Funções te relações.
*/
	public function has_many($class){
		if(is_array($this->array_has_many)){
			array_push($this->array_has_many, $class);
		} else {
			$this->array_has_many = array($class);
		}
		$this->updateRelationships();
	}
	public function has_one($class){
		if(is_array($this->array_has_one)){
			array_push($this->array_has_one, $class);	
		} else {
			$this->array_has_one = array($class);
		}
		$this->updateRelationships();
	}
	public function belongs_to($class, $tabela){
		$this->array_belongs_to[$tabela] = $class;
		$this->updateRelationships();
	}
	
	public function updateRelationships(){
		if(isset($this->id) and $this->id != ""){
			if(is_array($this->array_has_many)){
				foreach($this->array_has_many as $class){
					$classAll = $class."all";
					$this->$classAll = new $class;				
					$this->$classAll->where($this->tabela."_id = ".$this->id);
					$this->$class = $this->$classAll->plural;
				}	
			} else 
			{
				$this->array_has_many = array();
			}
			//Função para atualizar relações has_one
			if(is_array($this->array_has_one)){
				foreach($this->array_has_one as $class){
					$id = $this->id;
					$tabela = $this->tabela;
					$query = "$tabela"."_id = \"$id\"";
					$obj = new $class;
					$obj->where($query);
					$this->$class = $obj;	
				}	
			} else 
			{
				$this->array_has_one = array();
			}
			//Função para atualizar relações has_one
			if(is_array($this->array_belongs_to)){
				foreach($this->array_belongs_to as $tabela => $single){
					$obj = new $single;
					$attTabela = $tabela."_id";
					$obj->find($this->$attTabela);
					$this->$single = $obj;
				}	
			} else 
			{
				$this->array_belongs_to = array();
			}

					
		}	
	}
	public function getJoins(){
		return $this->joins;
	}
	public function getAddFields(){
		return $this->addFields;
	}
	///MAGIC METHODS
	 public function __set($name, $value)
    {
        
        $this->{$name} = $value;
    }


}

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
	protected $globalWhere = array(); //Array to fill with global conditions;


	public function __construct($table,$fields=false,$where=false,$array=array(),$plural=array(),$page=1,$qtnbypage=9999999999) {
		//Criando um objeto vazio
		$this->plural = array();
		$this->tabela = $table;
		$this->pk_field = $table."_id";
			
		//Criando um objeto vazio
		$this->plural = $plural;
		$this->qtnbypage = $qtnbypage;
		$db = new bffdbmanager;
		$this->dbmanager = $db;
		$this->listaAtts = array();
		$colunas = $this->dbmanager->fetch_columns($table);
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
			unset($class);
		}
		$this->update_fields();
		
		if($where){
			$this->where($where,$array,$page);
		} elseif(count($plural) > 0){
			$this->arrayToObject();
		}
		
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
	public function addJoin($table,$fields=array(),$on=""){
		$on = ($on) ? $on : $table.".".$table."_id = [t].".$table."_id";
		$this->joins[$table] = array(
			"fields" => $fields,
			"on" => $on
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
		$joins = "";
		$joinsObj = $this->joins;
		$addFieldsObj = $this->addFields;
		foreach ($joinsObj as $table => $join) {
			$on = str_replace("[t]",$this->tabela, $join['on']);
			$joins .= "LEFT JOIN $table on ($on) ";
			foreach($join['fields'] as $field => $name){
				$keys .= ", $table.$field as $name ";
			}
		}
		foreach ($addFieldsObj as $value => $key) {
			$key = str_replace("[t]", $this->tabela, $key);
			$keys .= ", $key as $value";
		}

		return $keys.$from.$joins;
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

		return array($query,$data);
	}
	public function get_from(){
		return " FROM ".$this->tabela." ";
	}
	
	
	protected function arrayToObject(){
		if(is_array($this->plural)){
			foreach ($this->plural as $key=>$atributos){
				$class = $this->single;
				
				if($class == 'dbModel'){
					$single = new dbModel($this->tabela);	

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
		$fim = $page + $this->qtnbypage - 1;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]." LIMIT $ini,$fim",$where[1]);
		$qtntotal = $this->dbmanager->query("SELECT COUNT({$this->pk_field}) FROM ".$this->tabela.$where[0], $where[1]);
		$qtntotal = $qtntotal[0][0]["COUNT({$this->pk_field})"];
		$this->nowinpage = $page;
		$this->last_query = "SELECT {$this->fieldsStr} FROM ".$this->tabela.$where[0];
		$this->total = $qtntotal;
		$this->plural = $resultados[0];
		$this->arrayToObject();
		$this->last_query = "SELECT {$this->fieldsStr} FROM ".$this->tabela.$where[0]." LIMIT $ini,$fim";
		$this->last_query_array = array();
		return $this->info();
	}
	public function last($n=1){
		$where = $this->globalWhereQuery("",array());
		$table = $this->tabela;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]." ORDER BY {$this->pk_field} DESC LIMIT $n",$where[1]);
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
		$fim = $page + $this->qtnbypage - 1;
		$resultados = $this->dbmanager->query("SELECT {$this->fieldsStr} ".$where[0]. " LIMIT $ini,$fim",$where[1]);
		$qtntotal = $this->dbmanager->query("SELECT COUNT({$this->pk_field}) FROM ".$this->tabela.$where[0], $where[1]);
		if (isset($qtntotal[0][0])) {
			$qtntotal = $qtntotal[0][0]["COUNT({$this->pk_field})"];
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