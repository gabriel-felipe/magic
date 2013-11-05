<?php
require_once(path_datamgr."/bff-dbconnect.php");

	//Default class for code generating
	class bffdbmanager {
		//defines attributes
		public $table;
		protected $cnx; //variable for the connection with db
		protected $cols; //must be an array that follow the shape $cols = array([0]=>["name" => $nome, "type" => $tipo, "lenght" => "lenght"]);
		//primary function
		public function __construct($table = false){
			$cnx = new bffdbconnect;
			$this->cnx = $cnx->connect();
			if(!$this->cnx){
				throw new Exception("Erro ao conectar com o banco de dados", 1);
				
			}
		}
		
		public function getTables($fetchColumns=false){
			$cnxQuery = $this->cnx->prepare("SHOW TABLES");
			$cnxQuery->execute();
			$cnxQuery->setFetchMode(PDO::FETCH_ASSOC);
			$resultados = $cnxQuery->fetchAll();
			$tables = array();
			foreach($resultados as $res){
				$table = array_pop($res);
				if ($fetchColumns) {
					$columns = $this->fetch_columns($table);
					$tables[$table] = $columns;
				} else {
					$tables[] = $table;
				}
				
			}
			return $tables;
		}

		public function has_table($table){
			
			$query = $this->cnx->query("SELECT * FROM $table");
		    if($query)
		    {
		        return true;

		    } else {
		    	$erro = $this->cnx->errorInfo();
				if($erro[1] == 1146){
					return false;
				} else {
					die ("A query possui algum problema, mais detalhes técnicos em: ".$this->cnx->errorInfo());
				}
		    }
			
		}
		public function duplicate_table($antiga, $nova){
			$has_table = $this->has_table($antiga);
			if($has_table){
				$this->cnx->query("SELECT * INTO $nova FROM $antiga WHERE 1=0");
			}else {
				echo "Can't duplicate something that doesn't exists";
			}
		}
		public function create_table($table){
			$has_table = $this->has_table($table);
			if(!$has_table){
				$this->cnx->query("create table $table (id int not null auto_increment,  primary key(id) )");
			}else {
				echo "Table already exists $table";
			}
		}
		public function drop_table($table){
			$has_table = $this->has_table($table);
			if($has_table){
				$this->cnx->query("DROP TABLE $table");
			}else {
				echo "Table don't  exists";
			}
		}
		public function add_column($table, $name, $type="varchar(255)", $null=true, $default=false){
			$has_table = $this->has_table($table);
			$null = ($null) ? "NULL" : "NOT NULL";
			$default = ($default) ? "DEFAULT \"$default\"" : "";
			if($has_table and !$this->has_column($table,$name)) {
				$query = "ALTER TABLE $table ADD $name $type $null $default";
				if(!$this->cnx->query($query)) {
					echo "<br />Error trying to add column($query) - ".print_r($this->cnx->errorInfo())."<br />";
				}
				
			}else {
				echo "Table doesn't exists";
			}
		}
		public function add_foreign_key($table, $column,$tableref, $columnref){
			if($this->has_table($table) and $this->has_table($tableref)){
				$this->cnx->query("ALTER TABLE '{$table}' ADD FOREIGN KEY ('{$column}') REFERENCES '{$tableref}' ('{$columnref}' )");
			}
		}
		public function add_columns($names = false, $types = false, $table){
			$has_table = $this->has_table($table);
			if(is_array($names)){
				if(!is_array($types) and $types){
					foreach($names as $name){
						$this->cnx->query("ALTER TABLE $table ADD $name $types");
					}
				} elseif (is_array($types) and $types) {
					foreach($names as $name=>$typekey){
					$type = $types[$typekey];
					$this->cnx->query("ALTER TABLE $table ADD $name $type");
					}
				} else {
					echo "Type wasn't defined";
				}
			}
			$has_table = $this->has_table($table);
			if($has_table){
				$this->cnx->query("ALTER TABLE $table ADD $name $type");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function query($query, $values=array()){
			
			$cnxQuery = $this->cnx->prepare($query);
			if($cnxQuery->execute($values)){
			$cnxQuery->setFetchMode(PDO::FETCH_ASSOC);
			$resultados = $cnxQuery->fetchAll();
			$qtnLinhas = $cnxQuery->rowCount();
			return array($resultados, $qtnLinhas);
			} else {
				echo $query;
				die(print_r($cnxQuery->errorInfo()));
				return false;
			}
		}
		public function query_group($query,$values=array()){
			$cnxQuery = $this->cnx->prepare($query);
			if($cnxQuery->execute($values)){
			$qtnLinhas = $cnxQuery->rowCount();
			$resultados = $cnxQuery->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
			return array($resultados, $qtnLinhas);
			} else {
				echo $query;
				die(print_r($cnxQuery->errorInfo()));
				return false;
			}
		}
		public function update_column_type($table, $column, $newtype){
			$has_table = $this->has_table($table);
			if($has_table){
				$this->cnx->query("ALTER TABLE $table ALTER COLUMN $column $newtype");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function drop_column($name, $table){
			$has_table = $this->has_table($table);
			if($has_table){
				$this->cnx->query("ALTER TABLE $table DROP $name");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function has_column($table,$column){
			if($this->has_table($table)){
			$q = $this->cnx->prepare("DESCRIBE $table");
			$q->execute();
			$table_fields = $q->fetchAll();
			$tem = false;
			foreach($table_fields as $field){
				if($field[0] == $column){
					$tem = true;
					break;
				}
			}
			return $tem;
			} else {
				throw new Exception("Error Processing Request, table $table doesnt exist", 1);
				
			}
		}
		public function fetch_columns($table){
			if($this->has_table($table)){
			$q = $this->cnx->prepare("DESCRIBE $table");
			$q->execute();
			$table_fields = $q->fetchAll();
			foreach($table_fields as $field){
				$fields[] = array("name"=>$field[0],"type"=>$field[1]);
			}
			return $fields;
			} else {
				throw new Exception("Error Processing Request, table $table doesnt exist", 1);
				
			}
		}
	}
?>