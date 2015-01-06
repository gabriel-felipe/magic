<?php
    class ControllerAbout extends publicController {

        public function index(){
        	$this->html->addLink($this->scope->getThemeCss("about.css"));
        	$this->render($this->getContent());

        }

    }
   
?>
