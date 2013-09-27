<?php 
	class controllerCommonHome extends ManagerController {
		public function index(){
            $this->basicLayoutTasks();
			$this->template = 'common/home';
            $this->load->model("Scopes");
            $this->load->model("Routes");
            $this->MRoutes->getRoutes();
            $scopes = $this->MScopes->getScopes();

            
			echo $this->html->render($this->get_content());
		}
		
	}
?>