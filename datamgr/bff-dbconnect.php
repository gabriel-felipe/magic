<?php
	//General class for connecting do a db
	class bffdbconnect {
		protected $dbtype;
		protected $dbhost;
		protected $dbname;
		protected $dbuser;
		protected $dbpass;
		protected $cnx;
		protected static $connected = false;
		protected static $nConnections = 0;
		public function __construct(){
			global $path_common;
			$this->dbtype = db_driver;
			$this->dbhost = db_host;
			$this->dbuser = db_user;
			$this->dbpass = db_password;
			$this->dbname = db_name;
		}
		public function connect(){
			try {
				if(!self::$connected){
					self::$nConnections++;
					$this->cnx = new PDO($this->dbtype.':host='.$this->dbhost.';dbname='.$this->dbname,$this->dbuser,$this->dbpass, array(PDO::ATTR_PERSISTENT => true) );				
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
