<?php
	namespace Publico\Controller;
    class Home extends \Publico\Controller {

        public function index(){

        	$this->render($this->getContent());
        	
        }

        public function test(){

        	$this->render($this->getContent());

        }

    }
?>