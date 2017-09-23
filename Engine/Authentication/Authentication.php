<?php 
namespace Magic\Engine\Authentication;
class Authentication
{
	protected $authenticator,$storage,$context="Default";
	function setAuthenticator(Authenticators\AbstractAuthenticator $authenticator){
		$this->authenticator = $authenticator;
		if ($this->storage instanceof Storage\AbstractStorage) {
			$this->authenticator->setStorage($this->storage);
		}
		return $this;
	}
	function getAuthenticator(){
		return $this->authenticator;
	}
	function setStorage(Storage\AbstractStorage $storage){
		$this->storage = $storage;
		$this->storage->setContext($this->context);
		if ($this->authenticator instanceof Authenticators\AbstractAuthenticator) {
			$this->authenticator->setStorage($this->storage);
		}
		return $this;
	}
	function setContext($context){
		$this->context = $context;
		if ($this->storage instanceof Storage\AbstractStorage) {
			$this->storage->setContext($context);
		}
		return $this;
	}
	function getContext(){
		return $this->context();
	}
	function getStorage(){
		return $this->storage;
	}

	function authenticate($username,$senha){
		return $this->authenticator->authenticate($username,$senha);
	}

	function isLogged(){
		$id = $this->storage->read();
		if($id instanceof Identity){
			$id->callHookChain("isLogged");
		}
		return $id;
	}
	function logout(){
		return $this->storage->clear();
	}
}
?>