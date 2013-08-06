<?php
require_once('engine/errors.php');
require_once('config/responsive.php');
require_once('config/default.php');
require_once('config/routes.php');
foreach ($routes as $ns => $rts) {
	foreach ($rts as $route => $params) {
		$params['ns'] = $ns;
		$url->add_shortcut($route,$params);
	}
}
foreach ($defaults as $var => $value) {
	if(!isset(${$var}))	${$var} = $value;
}
date_default_timezone_set("America/Sao_Paulo");
$path = str_replace($_SERVER["DOCUMENT_ROOT"], "",dirname(__FILE__));
// Path Constants
define('ns',$namespace);
define("path_root",$_SERVER["DOCUMENT_ROOT"]."$path");
define("path_scope",$_SERVER["DOCUMENT_ROOT"]."$path/$namespace");

if(!is_dir(path_scope)){
	die("Namespace '$namespace' doesn't exist");
}

define("path_base","http://".$_SERVER["SERVER_NAME"]."$path");
define("base_url","http://".$_SERVER["SERVER_NAME"]."$path");
define("path_cache",path_root."/cache");
define("path_uploads",path_root."/uploads");
define("path_library",path_root."/librarys");
define("path_engine_library",path_root."/engine/library");
define("path_datamgr",path_root."/datamgr");
define("path_models",path_root."/$namespace/model");
define("path_controllers",path_root."/$namespace/controller");
define("path_views",path_root."/$namespace/views");
define("base_views",path_base."/$namespace/views");

define("base_js",path_base."/$namespace/views/js");
define("base_js_engine",path_base."/engine/js");
define("path_js",path_root."/$namespace/views/js");
define("path_theme",path_views."/$theme");
if(!is_dir(path_theme)){
	die("Theme '$theme' doesn't exist".path_theme);
}
define("base_theme",base_views."/$theme");
define("path_template",path_theme."/template");
define("base_template",base_theme."/template");
define("base_images",base_theme."/image");
define("path_css",path_theme."/css");
define("path_engine_css",path_root."/engine/css");
define("base_css",base_theme."/css");
define("base_cache",path_base."/cache");




// Db Constants
define('db_driver', 'mysql');
define('db_host', 'localhost');
define('db_user', 'root');
define('db_password', 'password');
define('db_name', 'databasename');
define('db_prefix', '');


require_once('engine/action.php');
require_once('engine/magicHtml.php');
require_once('engine/controller.php');
require_once('engine/module.php');
require_once('engine/registry.php');
require_once('engine/loader.php');
require_once('engine/url.php');
require_once('datamgr/dbmodel.php');
require_once('librarys/functions.php'); // Loading default functions
?>