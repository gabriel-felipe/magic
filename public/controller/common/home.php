<?php 
	class controllerCommonHome extends Controller {
		public function index(){
			$this->add_children('common/home/indexb');
			$this->template = 'common/home';

			echo $this->html->render($this->get_content());
		}
		public function indexb(){
			$this->html->add_css_linked("teste.css");
			echo "oie"; 
		}
	}
?>