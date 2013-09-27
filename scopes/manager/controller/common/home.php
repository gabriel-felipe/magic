<?php 
	class controllerCommonHome extends ManagerController {
		public function index(){
            $this->basicLayoutTasks();
			$this->template = 'common/home';
            $this->load->model("Scopes");
            $this->load->model("Routes");
            $this->MRoutes->getRawRoutes();
            echo $this->url->get("common_home",array("id"=>32,"idb"=>"abc"));
            $scopes = $this->MScopes->getScopes();
			echo $this->html->render($this->get_content());
		}
		
	}
?>