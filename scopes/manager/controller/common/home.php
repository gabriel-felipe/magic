<?php 
	class controllerCommonHome extends ManagerController {
		public function index(){
            $this->basicLayoutTasks();
            $this->html->add_css_linked("home.css");
			$this->template = 'common/home';
            $this->load->model("Scopes");
            $this->load->model("Routes");
            $this->MRoutes->getRawRoutes();
            

            $scopes = $this->MScopes->getScopes();
			echo $this->html->render($this->get_content());
		}
		
	}
?>