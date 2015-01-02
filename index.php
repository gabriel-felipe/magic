<?php
	require("root_paths.php");
	define('APPLICATION_ENV', getenv("APPLICATION_ENV"));
	require_once('engine/config/init.php');
	$globalConfig = new MagicConfig;
	$globalConfig->addFolder(path_root."/config/defaults");
	$globalConfig->addFolder(path_root."/config/".APPLICATION_ENV);
	$globalConfig->loadInAllAs("db.json","database");
	$globalConfig->loadInAllAs("routes.json","routes");
	$globalConfig->loadInAllAs("themes.json","themes");
	$globalConfig->loadInAllAs("errors.json","errors");
	$globalConfig->loadInAll("config.json");

	if($globalConfig->projectDebug == 1){
		ini_set('display_errors',1); 
	 	error_reporting(E_ALL);
	 	ini_set('html_errors', 'On');
 	}
 	date_default_timezone_set($globalConfig->default_timezone);
 	define("AUTO_GENERATE_LANGUAGE_URLS", $globalConfig->auto_generate_language_urls);

 	require_once('engine/registry.php');
	$registry = new registry;
	
	require_once('engine/hooks/init.php');
	require_once('engine/compilador/init.php');
	require_once("engine/document/init.php");
	require_once('engine/language.php');
	require_once('engine/mvc/init.php');
	require_once('engine/library/data-cleaner.php');
	require_once('engine/scope/init.php');
	require_once('engine/log.php');
	require_once('engine/error.php');
	require_once('engine/functions.php');
	require_once('engine/loader.php');
	require_once('librarys/functions.php');
	require_once('datamgr/dbmodel.php');
	require_once('engine/plugin/init.php');

	$loader = new loader($registry);
	$registry->set('load',$loader);
	$registry->set("config",$globalConfig);
	
	$routes = $globalConfig->routes->getData();
	$url = new url;
	$url_amigavel = data::get('url','url');
	foreach ($routes as $scopeb => $rts) {
		foreach ($rts as $route => $params) {
			$params['scope'] = $scopeb;
			$url->add_shortcut($route,$params);
		}
	}
	$registry->set("url",$url);

	if(AUTO_GENERATE_LANGUAGE_URLS){
		language::generateUrls();
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
	$scope = new scope($scope,$registry);
	$registry->set("scope",$scope);
	$scope->init();

	define("magic_language",$scope->language->getLang());
	
	
	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	} elseif (array_key_exists("route", $_POST)){
		$route = $_POST['route'];
	}
	$route = str_replace("_","/",$route);
	$registry->set("route",$route);
	
	$action = new action($route,$scope,array(),$registry);	
	$registry->set("action",$action);
	$action->execute();