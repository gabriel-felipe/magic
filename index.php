<?php
	require('config/project.php');
	if(PROJECT_DEBUG == 1){
		ini_set('display_errors',1); 
	 	error_reporting(E_ALL);
	 	ini_set('html_errors', 'On');
 	}

	require_once('engine/url.php');
	require_once('engine/library/data-cleaner.php');
	$routes = file_get_contents("config/routes.json");
	$routes = json_decode($routes,true);    
	$url = new url;
	$url_amigavel = data::get('url','url');
	foreach ($routes as $scopeb => $rts) {
		foreach ($rts as $route => $params) {
			$params['scope'] = $scopeb;
			$url->add_shortcut($route,$params);
		}
	}
	
	if(!isset($_GET['scope'])){
		if(isset($_POST['scope'])) {
			$_GET['scope'] = $_POST['scope'];
		} else {
			$url->analyze($url_amigavel);	
		}
	}

	$scope = data::get('scope','url');
	
	require_once('init.php');
	
	$registry = new registry;
	$magicHtml = new magicHtml;
	$registry->set('html',$magicHtml);
	if(is_file(path_scope."/init.php")){
		
		require_once(path_scope."/init.php");
	}
	$registry->set('url',$url);
	$json = new json;
	$registry->set('json',$json);
	
	$loader = new loader($registry);
	$registry->set('load',$loader);
	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	} elseif (array_key_exists("route", $_POST)){
		$route = $_POST['route'];
	}


	require_once('engine/library/phpbrowsercap.php');
	use phpbrowscap\Browscap;
	$browser = new Browscap(path_cache);
	$browser = $browser->getBrowser();
	$registry->set('browser',$browser);
	$action = new action($route,array(),$registry);	
	$action->execute();
?>