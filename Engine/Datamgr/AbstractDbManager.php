<?php
namespace Magic\Engine\Datamgr;
use \Exception;
use \PDO;

	//Default class for code generating
	Abstract class AbstractDbManager {
		//defines attributes
		protected $dbConnect;
		protected $cnx; //variable for the connection with db
		protected $cols; //must be an array that follow the shape $cols = array([0]=>["name" => $nome, "type" => $tipo, "lenght" => "lenght"]);
		protected $logExecTimeFile=false;
		protected $minExecTimeToLog=0; //Todas as querys que demorarem mais que esse valor em segundos para executar serão logadas.
		protected $cacheQueryResults=false;
		protected $cacheLife = 3600; //in seconds
		protected $minExecTimeToCache = 0.0016;
		protected $cache = array();
		public $lastQuery = "";
		public $lastError = false;
		//primary function
		public function __construct(DbConnect $cnx){
			$this->dbConnect = $cnx;
			// $this->logExecTimeFile = path_root."/logs/queryexectime.log";
			$this->cacheQueryResults = false;
			$this->cnx = $cnx->connect();
			if(!$this->cnx){
				throw new Exception("Erro ao conectar com o banco de dados", 1);
			}
		}
		
		abstract public function getTables($fetchColumns=false);
		abstract public function hasTable($table);
		abstract public function duplicateTable($antiga, $nova);
		abstract public function createTable($table);
		abstract public function dropTable($table);
		abstract public function addColumn($table, $name, $type="varchar(255)", $null=true, $default=false);
		abstract public function addForeignKey($table, $column,$tableref, $columnref);
		abstract public function addColumns($names = false, $types = false, $table);
		abstract public function updateColumnType($table, $column, $newtype);
		abstract public function dropColumn($name, $table);
		abstract public function hasColumn($table,$column);
		abstract public function fetchColumns($table); //Return array of DbColumn Objects


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
				$this->lastError = $query;
				throw new DbException("Error executing query.", 1);
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
	}
?>