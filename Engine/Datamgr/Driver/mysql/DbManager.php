<?php
namespace Magic\Engine\Datamgr\Driver\mysql;
use Magic\Engine\Datamgr\AbstractDbManager;
use Magic\Engine\Datamgr\DbColumn;
use \Exception;
use \PDO;

	//Default class for code generating
	class DbManager extends AbstractDbManager {


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
			$query = $this->cnx->query("describe $table");
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
			$rawTable = $table;
			$table = trim($table,"`");
			$table="`".$table."`";
			if($this->hasTable($table)){
			$q = $this->cnx->prepare("DESCRIBE $table");
			$q->setFetchMode(PDO::FETCH_ASSOC);

			$q->execute();
			$table_fields = $q->fetchAll();
			$fields = array();
			$foreignKeys = "SELECT concat(table_name, '.', column_name) as 'foreign key', concat(referenced_table_name, '.', referenced_column_name) as 'references' from information_schema.key_column_usage where referenced_table_name is not null and table_name = '".$rawTable."' and table_schema = '".$this->dbConnect->dbname."'";
			$q = $this->cnx->prepare($foreignKeys);
			$q->setFetchMode(PDO::FETCH_ASSOC);

			$q->execute();
			$foreignKeys = $q->fetchAll();
			$fk = array();
			foreach($foreignKeys as $foreignKey){
				$fk[$foreignKey['foreign key']] = $foreignKey['references'];
			}
			foreach($table_fields as $field){
				$column = new DbColumn;
				$column->setName($field["Field"]);
				$column->setType($field["Type"]);
				if ($field["Null"] == "NO") {
					$column->setNull(false);
				} else {
					$column->setNull(true);
				}
				$column->setDefault($field['Default']);
				if ($field["Key"] == "PRI") {
					$column->setPrimaryKey(true);
				}
				if (array_key_exists($rawTable.".".$field['Field'], $fk)){
					$column->setReferences($fk[$rawTable.".".$field['Field']]);
				}
				if (strpos($field['Extra'], "auto_increment") !== false) {
					$column->setAutoIncrement(true);
				}


				$fields[] = $column;
			}
			return $fields;
			} else {
				throw new Exception("Error Processing Request, table $table doesnt exist", 1);

			}
		}
	}
?>
