<?php
if(!isset($_SESSION['login'])){
	$_SESSION['login'] = array();
}
class login {
	// DEFININDO VARIÁVEIS
	protected $table = "admin";
	protected $usernameField = "username";
	protected $passField = "password";
	protected $primaryField = "admin_id";
	protected $saltField = "salt";
	protected $groupField = "grupo_usuarios_id";
	protected $userpass, $db;
	public $username, $id;
	protected $userInfo = array();
	public $msgErro = "Login ou Senha inválidos";
	protected $registry;

	// DEFINIR AS INFORMAÇÕES DA CLASSE
	function __construct($registry) {
		$this->db = new bffdbmanager;
		$this->registry = $registry;
	}
	
	// FAZENDO LOGIN DO USUARIO
	function login($login,$pass) {
		unset($_SESSION['login']);
		$this->userpass = $pass;
		$this->username = $login;
		// Verifica se o usuário existe
		$query = $this->db->query("SELECT * FROM ".$this->table." WHERE ".$this->usernameField." = :user LIMIT 0,1", array(":user"=>$this->username));
		$campos = $query[1];
		$info = (isset($query[0][0])) ? $query[0][0] : array();
		// Se o usuário existir
		if($campos){	
			//Se a senha estiver incorreta
			if(crypt($this->userpass,$info['salt']) != $info[$this->passField]){
			$this->msgErro = "A senha está errada";
			$this->falhaLogin();
			return false;
			// Se a senha estiver correta
			}else{
				session_regenerate_id(true);
				$this->userpass = "";
				unset($info[$this->passField]);
				$this->userInfo = $info;
				if(isset($info[$this->primaryField])){
					$_SESSION['login']['user_id'] = $info[$this->primaryField];
				}
				$_SESSION['login']['user_info'] = $info;
				// Coloca as informações em sessões
				if($this->groupField){
					$_SESSION['login']['user_group'] = $info[$this->groupField];
					$this->registry_token($info[$this->groupField]);
				} else {
					$this->registry_token();
				}
				$_SESSION['login']['table'] = $this->table;
				$this->postLogin($info);
				unset($info);
				
				return true;
			}
		}
		// Se o usuário não existir
		else {
			$this->msgErro = "O usuário não existe!";
			$this->falhaLogin();
			return false;
		}
	}
	function registry_token($userGroup=""){
		$rand = mt_rand();
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $_SERVER['HTTP_USER_AGENT'];		
		$token = md5($rand.$ip.$browser);
		$_SESSION['login']['rand_token'] = $rand;
		$_SESSION['login']['token'] = $token;
		$_SESSION['login']['user_group'] = $userGroup;
		$_SESSION['login']['token_start'] = time();
		$_SESSION['login']['token_touch'] = time();
	}
	function is_logged($userGroup="",$table='default'){
		if($table==='default'){
			$table = $this->table;
		}
		if($table){
			if(!isset($_SESSION['login']['table']) or $_SESSION['login']['table'] != $table){
				return false;
			}
		}
		$rand = (isset($_SESSION['login']['rand_token'])) ? sanitize::special_chars($_SESSION['login']['rand_token']) : "";
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$token = md5($rand.$ip.$browser);
		$token_life = 60*60*8;
		$token_touch = (isset($_SESSION['login']['token_touch'])) ? $_SESSION['login']['token_touch'] : "";
		$expirou = (time() - sanitize::int($token_touch) > $token_life) ? true : false;
		if($this->groupField){
			$sessionUserGroup = (isset($_SESSION['login']['user_group'])) ? $_SESSION['login']['user_group'] : "";
			$grupo = ($userGroup == sanitize::special_chars($sessionUserGroup) or $userGroup == "") ? true : false;
		} else {
			$grupo = true;
		}

		if(isset($_SESSION['login']['token']) and $token == $_SESSION['login']['token'] and !$expirou and $grupo){
			if(isset($_SESSION['login']['pessoa_id'])){
				$pessoa = new pessoa;
				$pessoa->find($_SESSION['login']['pessoas_id']);
				$info = $pessoa->info();
				$this->registry->set("logged",$info);
				
			}
			$this->touch_token();
			return true;
		} else{
			if (!isset($_SESSION['login']['token']) or $token != $_SESSION['login']['token']){
				$this->msgErro = "Por favor faça o login";
			}
			elseif (!$grupo){

			 	$this->msgErro = "Seu grupo não possui esse nível de permissão $userGroup -".$_SESSION['login']['user_group'];
			} 
			elseif ($expirou){
				
				$this->msgErro = "Sua sessão expirou";
			}
			$this->logout();
			return false;
		}
	}
	protected function postLogin($info){

	}
	function touch_token(){
		$_SESSION['login']['token_touch'] = time();
	}
	// VERIFICA SE O USUÁRIO ESTÁ LOGADO
	function verificar($nivel = false) {
		// Se estiver logado
		if($this->check_token($nivel)){
			return true;
		} else {
			return false;
		}
	}
	function falhaLogin($redireciona = false){
		$alerta = $this->msgErro;
		$this->logout();
		// Se for necessário redirecionar
			if ($redireciona){
				echo "<meta http-equiv=\"refresh\" content=\"0; url=$redireciona\">";
			}
			
		return false;
		exit;
	}
	// LOGOUT
	public function logout($redireciona = false) {
		unset($_SESSION['login']['rand_token']);
		$this->username = "";
		$this->password = "";
		$this->userInfo = "";
		session_regenerate_id(true);  

		// Modifica o ID da Sessão
		if ($redireciona){
			$this->msgErro = "Deslogado do sistema com sucesso.";
			$this->falhaLogin($redireciona);
			exit;
		}
	}
	public function __get($key) {
		return $this->registry->get($key);
	}

}
?>