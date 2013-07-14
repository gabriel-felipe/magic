<?php
	class Controllercommonhome extends Controller{
		public function index(){
			
			if($this->login->is_logged("1")){

			$this->children = array("common/header", "common/footer");
			$this->add_css_linked('home.css');
			$this->template = 'common/home';
			
			echo $this->create();
			} else {
				$this->url->redirect('common/login', array("erro"=>$this->login->msgErro));
			}
		}
	}
?>