<?php
	class Controllercommonlogin extends Controller{
		public function index(){
			if($this->login->is_logged("1")){
				
				$this->url->redirect("common/home");
			}
			$this->add_css_linked("style_guide.css");
			$this->add_css_linked("style-guide-plus.css");
			$this->add_css_linked("font-awesome.css");
			$this->add_css_linked("login.css");
			$this->template = 'common/login';
			
			$this->data['erro'] = data::get('erro');
			$salt = rand();
			$this->data['action'] = $this->url->get('common/login/login');
			echo $this->create();


		} 
		public function login(){
			$username = data::post('username');
			$password = data::post('password');
			if($this->login->login($username,$password)){
				$link = (data::get("page","url")) ? data::get("page","url") : 'common/home';
				$this->url->redirect($link);
			} else {
				
				$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}

		}
	}
?>