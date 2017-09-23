<?php
	namespace Magic\Engine\Authentication\Storage;
	class Session extends AbstractStorage {
		public function write($identity){

			if (!isset($_SESSION['magicAuth'])) {
				$_SESSION['magicAuth'] = array();
			}
			$_SESSION['magicAuth'][$this->getContext()] = array(
				"identity" => serialize($identity),
				"token" => md5($_SERVER['HTTP_USER_AGENT']."-".$_SERVER['REMOTE_ADDR']),
				"touch" => time(),
			);

		}
		public function read(){
			if (isset($_SESSION['magicAuth'][$this->getContext()]) and $_SESSION['magicAuth'][$this->getContext()]){
				$token = md5($_SERVER['HTTP_USER_AGENT']."-".$_SERVER['REMOTE_ADDR']);
				if ($token == $_SESSION['magicAuth'][$this->getContext()]['token']) {
					if (time() - $_SESSION['magicAuth'][$this->getContext()]['touch'] < 60*120) {
						$_SESSION['magicAuth'][$this->getContext()]['touch'] = time();
						return unserialize($_SESSION['magicAuth'][$this->getContext()]['identity']);
					} else {
						$this->clear();
						return false;
					}
				} else {
					$this->clear();
					return false;
				}

			} else {
				$this->clear();
				return false;
			}

		}
		public function clear(){
			$_SESSION['magicAuth'][$this->getContext()] = null;
		}
		public function isEmpty(){
			return (isset($_SESSION['magicAuth'][$this->getContext()]) and $_SESSION['magicAuth'][$this->getContext()]) ? false : true;
		}
	}
?>
