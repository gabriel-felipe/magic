<?php
    class Controllerscoperoutes extends managerController {

        public function index(){
        	require(path_models."/Routes.php");
        	$routes = new Routes;
        	print_r($routes->getRawRoutes());
        }

    }
?>