<?php
	namespace Magic\Engine\Datamgr;
	use \Exception;
	use \PDO;
	use \PDOException;
	//General class for connecting do a db
	class DbConnect {
		public $dbtype;
		public $dbhost;
		public $dbname;
		public $dbuser;
		public $dbpass;
		public $cnx;
		protected static $connected = false;
		protected static $nConnections = 0;
		public function __construct($dbname=false,$dbpass=false,$dbuser=false,$dbhost=false,$dbtype=false){
			global $registry;
			$config = $registry->get("config");
			if (!$dbtype) {
				$dbtype = $config->database->get("db_driver");
			}
			if (!$dbhost) {
				$dbhost = $config->database->get("db_host");
			}
			if (!$dbuser) {
				$dbuser = $config->database->get("db_user");
			}
			if (!$dbpass) {
				$dbpass = $config->database->get("db_password");
			}
			if (!$dbname) {
				$dbname = $config->database->get("db_name");
			}

			$this->dbtype = $dbtype;
			$this->dbhost = $dbhost;
			$this->dbuser = $dbuser;
			$this->dbpass = $dbpass;
			$this->dbname = $dbname;
		}
		public function connect(){
			try {
				if(!self::$connected){
					$this->cnx = new PDO($this->dbtype.':host='.$this->dbhost.';dbname='.$this->dbname.";charset=utf8",$this->dbuser,$this->dbpass, array(PDO::ATTR_PERSISTENT => true) );				
					self::$connected  = $this->cnx;
				} else {
					$this->cnx = self::$connected;
				}
			} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			return false;
			}			
			return $this->cnx;
		}

	}
