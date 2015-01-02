<?php 
$root = str_replace("/",DIRECTORY_SEPARATOR,$_SERVER["DOCUMENT_ROOT"]);
$path = str_replace($root, "",dirname(__FILE__));
define("path_root",$root."$path");
define("base_url","http://".$_SERVER["SERVER_NAME"]."$path");
define("path_base","http://".$_SERVER["SERVER_NAME"]."$path");
define("path_cache",path_root.DIRECTORY_SEPARATOR."cache");
define("path_scopes",path_root.DIRECTORY_SEPARATOR."scopes");
define("path_uploads",path_root.DIRECTORY_SEPARATOR."uploads");
define("path_library",path_root.DIRECTORY_SEPARATOR."librarys");
define("path_engine",path_root.DIRECTORY_SEPARATOR."engine");
define("path_engine_library",path_root.DIRECTORY_SEPARATOR."engine".DIRECTORY_SEPARATOR."library");
define("path_datamgr",path_root.DIRECTORY_SEPARATOR."datamgr");
define("path_system_js",path_root.DIRECTORY_SEPARATOR."engine".DIRECTORY_SEPARATOR."js");
define("path_common_js",path_root.DIRECTORY_SEPARATOR."common".DIRECTORY_SEPARATOR."js");
define("path_common",path_root.DIRECTORY_SEPARATOR."common");
define("base_cache",path_base.DIRECTORY_SEPARATOR."cache");
define("path_log",path_root.DIRECTORY_SEPARATOR."logs");
define("path_engine_css",path_root.DIRECTORY_SEPARATOR."engine".DIRECTORY_SEPARATOR."css");
define("path_common_css",path_root.DIRECTORY_SEPARATOR."common".DIRECTORY_SEPARATOR."css");
define("base_js_engine",path_base.DIRECTORY_SEPARATOR."engine".DIRECTORY_SEPARATOR."js");

?>