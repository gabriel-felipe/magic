<?php
if(session_id() == ""){
session_start();	
}
require_once(path_models."/pessoas.php");
class login {
	// DEFININDO VARIÁVEIS
	private $table = "admin";
	private $usernameField = "username";
	private $passField = "password";
	private $primaryField = "admin_id";
	private $saltField = "salt";
	private $groupField = "grupo_usuarios_id";
	private $userpass, $db;
	public $username, $id;
	private $userInfo = array();
	public $msgErro = "Login ou Senha inválidos";
	private $registry;

	// DEFINIR AS INFORMAÇÕES DA CLASSE
	function __construct($registry) {
		$this->db = new bffdbmanager;
		$this->registry = $registry;
	}
	
	// FAZENDO LOGIN DO USUARIO
	function login($login,$pass) {
		
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
			$this->msgErro = "A senha está errada / ".crypt($this->userpass,$info['salt']);
			$this->falhaLogin();
			return false;
			// Se a senha estiver correta
			}else{
				session_regenerate_id(true);
				// Coloca as informações em sessões
				
				// $_SESSION['user_group'] = $info[$this->groupField];
				unset($info[$this->passField]);
				$this->userpass = "";
				$this->userInfo = $info;

				$_SESSION['pessoa_id'] = $info['pessoa_id'];
				$this->registry_token(/*$info[$this->groupField]*/);
				unset($info);
				// Se for necessário redirecionar
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
		$_SESSION['rand_token'] = $rand;
		$_SESSION['token'] = $token;
		$_SESSION['user_group'] = $userGroup;
		$_SESSION['token_start'] = time();
		$_SESSION['token_touch'] = time();
	}
	function is_logged($userGroup=""){

		$rand = data::session('rand_token','special_chars');
		$ip = $_SERVER['REMOTE_ADDR'];
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$token = md5($rand.$ip.$browser);
		$token_life = 60*60*8;
		$expirou = (time() - data::session('token_touch', 'int') > $token_life) ? true : false;
		// $grupo = ($userGroup == data::session('user_group','special_chars') or $userGroup == "") ? true : false;

		if(isset($_SESSION['token']) and $token == $_SESSION['token'] and !$expirou /*and $grupo*/){
			if($_SESSION['pessoas_id']){
				$pessoa = new pessoa;
				$pessoa->find($_SESSION['pessoas_id']);
				$info = $pessoa->info();
				$this->registry->set("logged",$info);
				
			}
			$this->touch_token();
			return true;
		} else{
			if (!isset($_SESSION['token']) or $token != $_SESSION['token']){
				$this->msgErro = "Por favor faça o login";
			}
			// elseif (!$grupo){

			// 	$this->msgErro = "Seu grupo não possui esse nível de permissão $userGroup -".$_SESSION['user_group'];
			// } 
			elseif ($expirou){
				
				$this->msgErro = "Sua sessão expirou";
			}
			$this->logout();
			return false;
		}
	}
	function touch_token(){
		$_SESSION['token_touch'] = time();
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
		unset($_SESSION['rand_token']);
		unset($_SESSION['token']);
		unset($_SESSION['user_group']);
		unset($_SESSION['token_start']);
		unset($_SESSION['token_touch']);
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