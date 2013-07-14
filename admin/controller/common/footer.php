<?php
class Controllercommonfooter extends Controller{
		public function index(){
			$this->template = 'common/footer';
			echo $this->get_content();
		}
	}
	?>