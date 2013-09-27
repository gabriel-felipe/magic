<?php 
	class controllerCommonHome extends ManagerController {
		public function index(){
            $this->basicLayoutTasks();
			$this->template = 'common/home';
            $this->load->model("Scopes");

            $scopes = $this->MScopes->getScopes();
            
            
			echo $this->html->render($this->get_content());
		}
		
	}
?>