<?php
	class Controllercommonlogout extends Controller{
		public function index(){
			$this->login->logout();
			$this->url->redirect("common/login");
		}
	}
?>