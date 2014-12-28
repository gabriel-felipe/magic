<?php
	ini_set("memory_limit","200M");
	require("root_paths.php");
	require('config/project.php');
	if(PROJECT_DEBUG == 1){
		ini_set('display_errors',1); 
	 	error_reporting(E_ALL);
	 	ini_set('html_errors', 'On');
 	}
 	require_once('engine/registry.php');
	require_once('engine/language.php');
	require_once('engine/url.php');
	require_once('engine/library/data-cleaner.php');
	$registry = new registry;



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
	$registry->set("url",$url);
	$language = new language($registry);
	if(AUTO_GENERATE_LANGUAGE_URLS){
		$language->generateUrls();
	}
	$registry->set("language",$language);
	if(!isset($_GET['scope'])){
		if(isset($_POST['scope'])) {
			$_GET['scope'] = $_POST['scope'];
		} else {
			$url->analyze($url_amigavel);	
		}
	}

	$scope = data::get('scope','url');
	
	require_once('init.php');
	$loader = new loader($registry);
	$registry->set('load',$loader);
	if(is_file(path_scope."/init.php")){
		
		require_once(path_scope."/init.php");
	}
	
	$language->init("br"); //If exist language br, that will be the default one, else it will select the first in alphabetical order.
	if(AUTO_GENERATE_LANGUAGE_URLS){
		$magic_language = data::get("magic_language");
		if($magic_language){
			$language->select($magic_language);
		}
	}
	define("magic_language",$language->getLang());
	$json = new json;
	$registry->set('json',$json);
	
	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	} elseif (array_key_exists("route", $_POST)){
		$route = $_POST['route'];
	}
	$route = str_replace("_","/",$route);
	$_GET['route'] = $route;
	require_once('engine/library/phpbrowsercap.php');
	use phpbrowscap\Browscap;
	$browser = new Browscap(path_cache);
	$browser = $browser->getBrowser();
	$registry->set('browser',$browser);
	$mobileDetect = new Mobile_Detect();
	$registry->set("mobileDetect",$mobileDetect);
   	require_once("engine/document/init.php");

	$action = new action($route,array(),$registry);	
	$registry->set("action",$action);
	$action->execute();