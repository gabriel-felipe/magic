<?php
    class Controllerhome extends ScopeController {

        public function index(){
        	$this->view->oie = "oie";
        	$this->render($this->getContent());
        }
        public function getPage(){
        	$json = $this->MAjax->getInstance();
        	$page = data::post("page");
        	if(in_array($page, $this->CheckPages->getPages())){
        		$view = $this->scope->getView("pages/".$page);
        		$json->page = $view->render();
        		$json->pageName = $page;
        		$json->render();
        	} else {
				$json->setStatusCode(500);        
				$json->render();
			}
        }

    }
?>