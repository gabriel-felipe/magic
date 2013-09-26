<?php 
	class controllerCommonHome extends Controller {
		public function index(){
			$this->template = 'common/home';
			echo $this->html->render($this->get_content());
		}
		
	}
?>