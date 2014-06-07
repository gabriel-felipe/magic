<?php
if(session_id() == ""){
session_start();	
}
$jsonConfigs = array("db","defaults","themes");
foreach($jsonConfigs as $fileConfig){

${$fileConfig} = file_get_contents("config/$fileConfig.json");

${$fileConfig} = json_decode(${$fileConfig},true);    
}

foreach($db as $constant=>$value)
    define($constant,$value);
require_once('config/responsive.php');

foreach ($defaults as $var => $value) {
    if(!isset(${$var}) and $var)    ${$var} = $value;
}
$path = str_replace($_SERVER["DOCUMENT_ROOT"], "",dirname(__FILE__));
// Path Constants
define('scope',$scope);
define("path_scope",$_SERVER["DOCUMENT_ROOT"]."$path/scopes/$scope");
if(!is_dir(path_scope)){
    die("scope '$scope' doesn't exist");
}



define("path_models",path_root."/scopes/$scope/model");
define("path_controllers",path_root."/scopes/$scope/controller");
define("path_views",path_root."/scopes/$scope/views");
define("base_views",path_base."/scopes/$scope/views");

define("base_js",path_base."/scopes/$scope/views/js");
define("base_common_js",path_base."/scopes/$scope/views/js");
define("base_js_engine",path_base."/engine/js");
define("path_js",path_root."/scopes/$scope/views/js");
if(isset($themes[$scope])){
    $theme = $themes[$scope];
}
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
define("path_common_css",path_root."/common/css");
define("base_css",base_theme."/css");
define("base_cache",path_base."/cache");

define("path_log",path_root."/logs");
require_once('engine/log.php');
require_once('engine/error.php');
require_once('engine/action.php');
require_once('engine/functions.php');
require_once('engine/magicHtml.php');
require_once('engine/json.php');
require_once('engine/controller.php');
require_once('engine/module.php');
require_once('engine/loader.php');
require_once('librarys/functions.php');
require_once('datamgr/dbmodel.php');

?>