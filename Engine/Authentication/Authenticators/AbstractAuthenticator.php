<?php 
	namespace Magic\Engine\Authentication\Authenticators;

	abstract class AbstractAuthenticator {
		protected $storage;
		protected $identity;
		protected $error;
		final public function authenticate($username,$password){
			if (!$this->identity) {
				throw new \LogicException("Please set the authenticator identity before authenticate.", 1);
				
			}
			$data = $this->isValid($username,$password);
			if ($data !== false and !is_array($data)) {
				throw new \LogicException("IsValid method is suposed to return an array or boolean false.", 1);
			}
			if ($data) {
				$this->populateIdentity($username, $data);
				return $this->identity;
			} else {
				$this->storage->clear();
				return false;
			}
		}
		abstract public function isValid($username,$password);
		public function setStorage(\Magic\Engine\Authentication\Storage\AbstractStorage $storage){
			$this->storage = $storage;
			return $this;
		}
		public function setIdentity(\Magic\Engine\Authentication\Identity $identity){
			$this->identity = $identity;
			return $this;
		}
		public function getIdentity(){
			return $this->identity;
		}
		public function populateIdentity($username,array $data){
			$identity = $this->getIdentity();
			$identity->setCredential($username);
			$identity->logMeIn($data);
			$this->storage->write($identity);
			return $this;
		}
		public function getStorage($storage){
			return $this->storage;
		}
		protected function setError($error){
			$this->error = $error;
			return $this;
		}
		public function getError(){
			return $this->error;
		}
	}
?>