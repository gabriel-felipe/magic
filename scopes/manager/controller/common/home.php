<?php 
	class controllerCommonHome extends ManagerController {
		public function index(){
            $this->basicLayoutTasks();
            $this->html->add_css_linked("home.css");
			$this->template = 'common/home';
            $this->load->model("Scopes");
            $this->load->model("Routes");
            $this->MRoutes->getRawRoutes();
            $dbConfig = file_get_contents(path_root."/config/db.json");
            $dbConfig = json_decode($dbConfig,true);
            $this->data['dbConfig'] = $dbConfig;
            $this->data['writabbleDirs'] = $this->writabbleDirs();
            $scopes = $this->MScopes->getScopes();
            $this->data['scopes'] = $scopes;
			echo $this->html->render($this->get_content());
		}
        public function writabbleDirs(){
            $writtableDirs = array(path_cache,path_root."/config",path_root."/logs",path_root."/scopes");
            $res = array();
            $allTrue = true;
            foreach($writtableDirs as $dir){
                if(is_writable($dir)){
                    $res[$dir] = true;
                } else {
                    $allTrue = false;
                    $res[$dir] = false;
                }
            }
            
            return $res;
        }
        public function teste(){
            echo $this->json->success("teste",array("info"=>5));
        }
		
	}
?>