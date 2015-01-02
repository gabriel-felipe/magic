<?php
namespace Magic\Engine\Datamgr;
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
	public $groupBy = false;
		public function __construct($tabela,$fields=false,$query=false,$queryParams=false) {
			$tabela = str_replace("`","",$tabela);
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
			$db = new DbManager;
			$this->dbmanager = $db;
			
			
			$this->listaAtts = array();
			$colunas = $this->dbmanager->fetch_columns("`".$tabela."`");
			foreach($colunas as $coluna){
				if($coluna['name'] == $this->pk_field or !$fields or (is_array($fields) and in_array($coluna['name'], $fields)) or $fields == $coluna['name'] or $fields == "*" ){
					$this->listaAtts[$coluna['name']] = "";
				}
			}
			$this->setAtributos();
			$this->tabela = "`".$tabela."`";
			$this->sanitizer = new sanitize;
			$this->validator = new validate;
			if(is_int($query) and $query > 0){
				$this->find($query);
			} elseif(is_string($query) and $query and is_array($queryParams)){
				$this->where($query,$queryParams);
			}

		}

	public function getLastQuery(){
		return $this->dbmanager->lastQuery;
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
			if($this->{$this->pk_field} == ""){

				foreach($this->listaAtts as $att => $value){
					$listaCampos .= $att.",";
					$listaValores .= ":$att,";
				}
				$listaCampos = substr($listaCampos, 0, -1);
				$listaValores = substr($listaValores,0, -1);
				$query = "insert into ".$this->tabela."($listaCampos) VALUES ($listaValores)";
			} else 
			{
				unset($valores[':'.$this->pk_field]);
				$this->before_update();
				$query = "UPDATE ".$this->tabela." set ";
				foreach($this->listaAtts as $att => $value){
					if($att != $this->pk_field){
						$query .= "$att = :$att, ";
					}
				}
				$query = substr($query, 0, -2);
				$query .= " WHERE {$this->pk_field} = ".$this->{$this->pk_field}."";
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
	public function addJoin($table,$fields=array(),$on="",$method='left'){
		$on = ($on) ? $on : $table.".".$table."_id = [t].".$table."_id";
		$this->joins[$table] = array(
			"fields" => $fields,
			"on" => $on,
			'method'=>$method
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
			$joins .= $join['method']." JOIN $table on ($on) ";
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
	public function globalWhereQuery($query="",$data=array()){
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
		$where = $this->globalWhereQuery();
		$query = "SELECT $fields {$where[0]} ORDER by {$this->tabela}.{$this->pk_field} DESC LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query,$where[1]);
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
		$where = $this->globalWhereQuery();
		$query = "SELECT $fields {$where[0]} ORDER by RAND() LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query,$where[1]);
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
		$where = $this->globalWhereQuery();
		$query = "SELECT $fields {$where[0]} ORDER by {$this->pk_field} ASC LIMIT 1";
		$buscaSingle = $this->dbmanager->query($query,$where[1]);
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
		$where = $this->globalWhereQuery($query,$array);
		$q = "SELECT $fields {$where[0]} LIMIT 1";
		$buscaSingle = $this->dbmanager->query($q, $where[1]);
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
	public function post_info($info){
		return $info;
	}
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
		return $this->post_info($info);
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
?>