<?php
	//DEFAULT NS
	ini_set('display_errors',1); 
 	error_reporting(E_ALL);
 	ini_set('html_errors', 'On');
	require_once('librarys/data-cleaner.php');
	require_once('engine/url.php');
	$url = new url;
	$url_amigavel = data::get('url','url');
	if(preg_match('/^([a-z-]+)\/?$/', $url_amigavel,$match)){
		if(is_dir($url_amigavel)){
			if(!data::get('ns','url')){
				$_GET['ns'] = $match[1];
				
			}
		}
	}
	$ns = data::get('ns','url');
	if($ns){
		$namespace = $ns;
	} else {
		if(data::post('ns','url')){
			$namespace = data::post('ns','url');	
		}
	}
	require_once('init.php');
	$registry = new registry;
	$magicHtml = new magicHtml;
	$registry->set('html',$magicHtml);
	if(is_file(path_scope."/init.php")){
		require_once(path_scope."/init.php");
	}
	$url->set_ns(ns);
	if($url_amigavel){
		$url->analyze($url_amigavel);
	} else {
		$url->analyze("");
	}
	$registry->set('url',$url);
	
	$loader = new loader($registry);
	$registry->set('load',$loader);
	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	}
	if(array_key_exists("route", $_POST)){
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