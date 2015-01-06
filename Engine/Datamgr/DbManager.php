<?php
namespace Magic\Engine\Datamgr;
use \Exception;
use \PDO;

	//Default class for code generating
	class DbManager {
		//defines attributes
		protected $cnx; //variable for the connection with db
		protected $cols; //must be an array that follow the shape $cols = array([0]=>["name" => $nome, "type" => $tipo, "lenght" => "lenght"]);
		protected $logExecTimeFile=false;
		protected $minExecTimeToLog=0; //Todas as querys que demorarem mais que esse valor em segundos para executar serão logadas.
		protected $cacheQueryResults=false;
		protected $cacheLife = 3600; //in seconds
		protected $minExecTimeToCache = 0.0016;
		protected $cache = array();
		public $lastQuery = "";
		//primary function
		public function __construct(DbConnect $cnx){
			// $this->logExecTimeFile = path_root."/logs/queryexectime.log";
			$this->cacheQueryResults = false;
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
					$columns = $this->fetchColumns($table);
					$tables[$table] = $columns;
				} else {
					$tables[] = $table;
				}
				
			}
			return $tables;
		}

		public function hasTable($table){
			$table = trim($table,"`");
			$table="`".$table."`";
			$query = $this->cnx->query("SELECT * FROM $table");
		    if($query)
		    {
		        return true;

		    } else {
		    	$erro = $this->cnx->errorInfo();
				if($erro[1] == 1146){
					return false;
				} else {
					die ("A query possui algum problema, mais detalhes técnicos em: ".(print_r($this->cnx->errorInfo(),1)));
				}
		    }
			
		}
		public function duplicateTable($antiga, $nova){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($antiga);
			if($hasTable){
				$this->cnx->query("SELECT * INTO $nova FROM $antiga WHERE 1=0");
			}else {
				echo "Can't duplicate something that doesn't exists";
			}
		}
		public function createTable($table){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
			if(!$hasTable){
				$this->cnx->query("create table $table (id int not null auto_increment,  primary key(id) )");
			}else {
				echo "Table already exists $table";
			}
		}
		public function dropTable($table){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
			if($hasTable){
				$this->cnx->query("DROP TABLE $table");
			}else {
				echo "Table don't  exists";
			}
		}
		public function addColumn($table, $name, $type="varchar(255)", $null=true, $default=false){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
			$null = ($null) ? "NULL" : "NOT NULL";
			$default = ($default) ? "DEFAULT \"$default\"" : "";
			if($hasTable and !$this->hasColumn($table,$name)) {
				$query = "ALTER TABLE $table ADD $name $type $null $default";
				if(!$this->cnx->query($query)) {
					echo "<br />Error trying to add column($query) - ".print_r($this->cnx->errorInfo())."<br />";
				}
				
			}else {
				echo "Table doesn't exists";
			}
		}
		public function addForeignKey($table, $column,$tableref, $columnref){
			$table = trim($table,"`");
			$table="`".$table."`";
			if($this->hasTable($table) and $this->hasTable($tableref)){
				$this->cnx->query("ALTER TABLE '{$table}' ADD FOREIGN KEY ('{$column}') REFERENCES '{$tableref}' ('{$columnref}' )");
			}
		}
		public function addColumns($names = false, $types = false, $table){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
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
			$hasTable = $this->hasTable($table);
			if($hasTable){
				$this->cnx->query("ALTER TABLE $table ADD $name $type");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function query($query, $values=array()){
			$this->lastQuery = $query;
			list($usec, $sec) = explode(' ', microtime());
			$script_start = (float) $sec + (float) $usec;
			
			$id = md5($query).".".md5(serialize($values));
			if ($this->cacheQueryResults ) {
				if (isset($this->cache[$id]) and time() - $this->cache[$id]['time'] <= $this->cacheLife) {
					return array($this->cache[$id]['resultados'], $this->cache[$id]['qtnLinhas']);
				} elseif (is_file($this->cacheQueryResults.$id.".cache")) {
				
					$content = unserialize(file_get_contents($this->cacheQueryResults.$id.".cache"));
					$this->cache[$id] = $content;
					if (time() - $content['time'] <= $this->cacheLife) {
						return array($content['resultados'], $content['qtnLinhas']);
					}
				}
			}
			$cnxQuery = $this->cnx->prepare($query);
			if($cnxQuery->execute($values)){
			$cnxQuery->setFetchMode(PDO::FETCH_ASSOC);
			$resultados = $cnxQuery->fetchAll();
			$qtnLinhas = $cnxQuery->rowCount();
			list($usec, $sec) = explode(' ', microtime());
			$script_end = (float) $sec + (float) $usec;
			$elapsed_time = round($script_end - $script_start, 5);
			if ($this->logExecTimeFile) {
				
				if ($elapsed_time >= $this->minExecTimeToLog) {
					$str = "\n<newquery>\n";
					$str .= $elapsed_time;
					$str .= "\n<newline>\n";
					$str .= $query;
					$str .= "\n<newline>\n";
					$str .= print_r($values,true);
					$file = fopen($this->logExecTimeFile, "a+");
					fwrite($file, $str);
					fclose($file);
				}
				

			}
			if ($this->cacheQueryResults and $elapsed_time >= $this->minExecTimeToCache) {
				$cache = array("time"=>time(),"resultados"=>$resultados,"qtnLinhas"=>$qtnLinhas);	
				$file  = fopen($this->cacheQueryResults."$id.cache", "w+");
				$str =  serialize($cache);
				fwrite($file, $str);
				fclose($file);
			}
			return array($resultados, $qtnLinhas);
			} else {
				echo $query;
				die(print_r($cnxQuery->errorInfo()));
				return false;
			}
		}
		public function queryGroup($query,$values=array()){
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
		public function updateColumnType($table, $column, $newtype){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
			if($hasTable){
				$this->cnx->query("ALTER TABLE $table ALTER COLUMN $column $newtype");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function dropColumn($name, $table){
			$table = trim($table,"`");
			$table="`".$table."`";
			$hasTable = $this->hasTable($table);
			if($hasTable){
				$this->cnx->query("ALTER TABLE $table DROP $name");
			}else {
				echo "Table doesn't exists";
			}
		}
		public function hasColumn($table,$column){
			$table = trim($table,"`");
			$table="`".$table."`";
			if($this->hasTable($table)){
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
		public function fetchColumns($table){
			$table = trim($table,"`");
			$table="`".$table."`";
			if($this->hasTable($table)){
			$q = $this->cnx->prepare("DESCRIBE $table");
			$q->execute();
			$table_fields = $q->fetchAll();
			$fields = array();
			foreach($table_fields as $field){
				$fields[] = array("name"=>$field[0],"type"=>$field[1],"key"=>$field[3]);
			}
			return $fields;
			} else {
				throw new Exception("Error Processing Request, table $table doesnt exist", 1);
				
			}
		}
	}
?>