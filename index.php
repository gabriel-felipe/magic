<?php
	ini_set('display_errors',1); 
 	error_reporting(E_ALL);
 	ini_set('html_errors', 'On');
	require_once('librarys/data-cleaner.php');
	require_once('engine/url.php');
	

	$url_amigavel = data::get('url','url');
	$url = new url;

	if($url_amigavel){
		$url->analyze($url_amigavel);
	}
	$ns = data::get('ns','url');
	if($ns){
		$namespace = $ns;
	} else {
		if(data::post('ns','url')){
			$namespace = data::post('ns','url');	
		} else {
			$namespace = "public";
		}
	} //Se tiver namespace definido na url

	if($namespace == 'admin'){
		$theme = "default";
		$route = 'common/login';
	} else {
		$theme = "default";
		$route = 'common/home';
	}

	require_once('init.php');	
	$url->set_ns(ns);

	$registry = new registry;
	$registry->set('url',$url);
	$magicHtml = new magicHtml;
	$registry->set('html',$magicHtml);
	$loader = new loader($registry);
	$registry->set('load',$loader);

	if(array_key_exists("route", $_GET)){
		$route = $_GET['route'];
	}
	if(array_key_exists("route", $_POST)){
		$route = $_POST['route'];
	}
	// if($namespace == 'admin'){
	// 	$loader->library('login');
	// 	$login = new login($registry);
	// 	$registry->set("login",$login);
	// }
	

	require_once('engine/library/phpbrowsercap.php');

	use phpbrowscap\Browscap;

	$browser = new Browscap(path_cache);

	$browser = $browser->getBrowser();
	
	$registry->set('browser',$browser);
	
	$action = new action($route,array(),$registry);	
	$action->execute();
?>